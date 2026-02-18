<?php

namespace App\Services;

use App\Models\Resident;
use App\Models\ResidentOffense;
use App\Models\UserOffense;
use App\Models\UserRestriction;
use App\Models\DocumentTypeRestriction;
use App\Models\EventAssistanceRestriction;
use App\Models\DocumentType;
use Illuminate\Support\Str;

class BackgroundCheckService
{
    /**
     * Check if a registrant matches any resident in the database
     * Matches by name (case-insensitive) and birthdate (exact match)
     * 
     * @param string $firstName
     * @param string|null $middleName
     * @param string $lastName
     * @param string|null $suffix
     * @param string $birthdate (Y-m-d format)
     * @return Resident|null
     */
    public function findMatchingResident(string $firstName, ?string $middleName, string $lastName, ?string $suffix, string $birthdate): ?Resident
    {
        // Normalize names for comparison (trim, lowercase)
        $normalize = function($str) {
            return $str ? strtolower(trim($str)) : '';
        };

        $firstNameNorm = $normalize($firstName);
        $middleNameNorm = $normalize($middleName);
        $lastNameNorm = $normalize($lastName);
        $suffixNorm = $normalize($suffix);

        // Find residents with matching birthdate
        $residents = Resident::whereDate('birthdate', $birthdate)->get();

        foreach ($residents as $resident) {
            $residentFirstName = $normalize($resident->first_name);
            $residentMiddleName = $normalize($resident->middle_name);
            $residentLastName = $normalize($resident->last_name);
            $residentSuffix = $normalize($resident->suffix);

            // Exact match (case-insensitive)
            if ($residentFirstName === $firstNameNorm &&
                $residentMiddleName === $middleNameNorm &&
                $residentLastName === $lastNameNorm &&
                $residentSuffix === $suffixNorm) {
                return $resident;
            }
        }

        return null;
    }

    /**
     * Calculate restrictions based on resident's offenses
     * 
     * @param Resident $resident
     * @return array Contains restriction configuration
     */
    public function calculateRestrictions(Resident $resident): array
    {
        // Get all active offenses for this resident
        $offenses = $resident->activeOffenses()->get();

        if ($offenses->isEmpty()) {
            return [
                'has_restrictions' => false,
                'highest_severity' => null,
                'restrict_document_request' => false,
                'restrict_event_assistance_request' => false,
                'restricted_document_types' => [],
                'restricted_event_types' => [],
                'offenses' => [],
            ];
        }

        // Find highest severity level
        $severities = ['Low' => 1, 'Medium' => 2, 'High' => 3];
        $highestSeverity = 'Low';
        $highestSeverityValue = 1;

        foreach ($offenses as $offense) {
            $severityValue = $severities[$offense->severity_level] ?? 1;
            if ($severityValue > $highestSeverityValue) {
                $highestSeverityValue = $severityValue;
                $highestSeverity = $offense->severity_level;
            }
        }

        // Only Medium and High offenses cause restrictions
        $hasRestrictions = in_array($highestSeverity, ['Medium', 'High']);

        if (!$hasRestrictions) {
            return [
                'has_restrictions' => false,
                'highest_severity' => $highestSeverity,
                'restrict_document_request' => false,
                'restrict_event_assistance_request' => false,
                'restricted_document_types' => [],
                'restricted_event_types' => [],
                'offenses' => $offenses->toArray(),
            ];
        }

        // Get restricted document types
        $restrictedDocumentTypes = $this->getRestrictedDocumentTypes($offenses);
        
        // Get restricted event assistance types
        $restrictedEventTypes = $this->getRestrictedEventTypes($offenses);

        // Determine if general restrictions should apply
        $restrictDocumentRequest = !empty($restrictedDocumentTypes);
        $restrictEventAssistanceRequest = !empty($restrictedEventTypes);

        return [
            'has_restrictions' => true,
            'highest_severity' => $highestSeverity,
            'restrict_document_request' => $restrictDocumentRequest,
            'restrict_event_assistance_request' => $restrictEventAssistanceRequest,
            'restricted_document_types' => $restrictedDocumentTypes,
            'restricted_event_types' => $restrictedEventTypes,
            'offenses' => $offenses->toArray(),
        ];
    }

    /**
     * Get list of document type IDs that should be restricted based on offenses
     * 
     * @param \Illuminate\Database\Eloquent\Collection $offenses
     * @return array Array of document_type_id values
     */
    protected function getRestrictedDocumentTypes($offenses): array
    {
        $restrictedDocTypeIds = [];

        // Get all document types that require clean records (no Medium/High offenses)
        $documentRestrictions = DocumentTypeRestriction::whereNotNull('required_offense_severity')
            ->whereIn('required_offense_severity', ['Medium', 'High'])
            ->get();

        // Check if user has Medium or High offenses
        $hasMediumOrHighOffenses = $offenses->whereIn('severity_level', ['Medium', 'High'])->isNotEmpty();

        if ($hasMediumOrHighOffenses) {
            // User has Medium/High offenses, so restrict all documents that require clean records
            foreach ($documentRestrictions as $restriction) {
                $restrictedDocTypeIds[] = $restriction->fk_document_type_id;
            }
        }

        return array_unique($restrictedDocTypeIds);
    }

    /**
     * Get list of event assistance types that should be restricted based on offenses
     * 
     * @param \Illuminate\Database\Eloquent\Collection $offenses
     * @return array Array of event type names
     */
    protected function getRestrictedEventTypes($offenses): array
    {
        $restrictedEventTypes = [];

        // Get all offense types from the user's offenses
        $offenseTypes = $offenses->pluck('offense_type')->toArray();
        $severityLevels = $offenses->pluck('severity_level')->unique()->toArray();

        // Find all event restrictions that match the user's offenses
        foreach ($offenseTypes as $offenseType) {
            // Get the severity level for this offense
            $offense = $offenses->firstWhere('offense_type', $offenseType);
            $severity = $offense ? $offense->severity_level : null;

            if (!$severity || !in_array($severity, ['Medium', 'High'])) {
                continue; // Only Medium/High restrict
            }

            // Find all event restrictions for this offense type and severity
            $eventRestrictions = EventAssistanceRestriction::where('offense_type', $offenseType)
                ->where('severity_level', $severity)
                ->get();

            foreach ($eventRestrictions as $restriction) {
                if (!in_array($restriction->event_type, $restrictedEventTypes)) {
                    $restrictedEventTypes[] = $restriction->event_type;
                }
            }
        }

        return array_unique($restrictedEventTypes);
    }

    /**
     * Apply restrictions to a user based on background check results
     * 
     * @param int $userId
     * @param array $restrictionData From calculateRestrictions()
     * @param int|null $restrictedBy Admin user ID who applied the restriction
     * @return UserRestriction
     */
    public function applyRestrictions(int $userId, array $restrictionData, ?int $restrictedBy = null): UserRestriction
    {
        // Build restriction reason
        $reason = "Automatic restriction based on background check. ";
        if (!empty($restrictionData['offenses'])) {
            $offenseTypes = array_column($restrictionData['offenses'], 'offense_type');
            $reason .= "Offenses found: " . implode(', ', array_unique($offenseTypes));
        }

        // Create or update user restrictions
        $userRestriction = UserRestriction::updateOrCreate(
            ['user_id' => $userId],
            [
                'restrict_document_request' => $restrictionData['restrict_document_request'] ?? false,
                'restrict_event_assistance_request' => $restrictionData['restrict_event_assistance_request'] ?? false,
                'restricted_document_types' => $restrictionData['restricted_document_types'] ?? [],
                'restricted_event_types' => $restrictionData['restricted_event_types'] ?? [],
                'restriction_reason' => $reason,
                'restricted_by' => $restrictedBy,
            ]
        );

        return $userRestriction;
    }

    /**
     * Copy resident offenses to user_offenses table when user is created
     * 
     * @param int $userId
     * @param Resident $resident
     * @return void
     */
    public function copyOffensesToUser(int $userId, Resident $resident): void
    {
        $offenses = $resident->activeOffenses()->get();

        foreach ($offenses as $offense) {
            UserOffense::create([
                'fk_user_id' => $userId,
                'fk_resident_offense_id' => $offense->offense_id,
                'offense_type' => $offense->offense_type,
                'severity_level' => $offense->severity_level,
                'offense_date' => $offense->offense_date,
                'status' => $offense->status,
            ]);
        }
    }
}


