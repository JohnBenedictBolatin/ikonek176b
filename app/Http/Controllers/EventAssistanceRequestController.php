<?php

namespace App\Http\Controllers;

use App\Models\EventAssistanceRequest;
use App\Models\EventAssistanceRequestItem;
use App\Models\EventAssistanceAvailableItem;
use App\Models\EventAssistanceAttachment;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class EventAssistanceRequestController extends Controller
{
    public function store(Request $request)
    {
        // Ensure purpose column is LONGTEXT before starting transaction
        // Do this outside transaction to avoid transaction conflicts
        try {
            DB::statement('ALTER TABLE event_assistance_requests MODIFY COLUMN purpose LONGTEXT NULL');
        } catch (\Exception $e) {
            // Column might already be LONGTEXT, ignore error
            \Log::debug('Purpose column type check: ' . $e->getMessage());
        }
        
        \Log::info('=== EVENT ASSISTANCE REQUEST STORE CALLED ===', [
            'user_id' => Auth::id(),
            'has_valid_id_content' => $request->hasFile('valid_id_content'),
            'has_id_front' => $request->hasFile('id_front'),
            'has_id_back' => $request->hasFile('id_back'),
            'has_valid_id' => $request->hasFile('valid_id'),
            'input_keys' => array_keys($request->all()),
            'all_files_count' => count($request->allFiles()),
            'all_files_keys' => array_keys($request->allFiles()),
        ]);

        try {
            $userId = Auth::id();
            if ($userId) {
                // Check if user is restricted from event assistance requests
                $restriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
                if ($restriction && $restriction->restrict_event_assistance_request) {
                    return redirect()->back()->with('error', 'You are restricted from making event assistance requests. Please check your notifications for more information.');
                }
                
                // Check if the specific event type is restricted
                $eventType = $request->input('event_type', '');
                if (!empty($eventType) && $restriction) {
                    $restrictedEventTypes = $restriction->restricted_event_types ?? [];
                    $allowedEventTypes = $restriction->allowed_event_types ?? [];
                    
                    $isRestricted = in_array($eventType, $restrictedEventTypes);
                    $isAllowed = !empty($allowedEventTypes) && in_array($eventType, $allowedEventTypes);
                    
                    // If restricted and not explicitly allowed, reject the request
                    if ($isRestricted && !$isAllowed) {
                        \Log::warning('User attempted to request restricted event type', [
                            'user_id' => $userId,
                            'event_type' => $eventType,
                        ]);
                        
                        return redirect()->back()
                            ->withInput()
                            ->with('error', "You are restricted from requesting {$eventType}. Please contact the admin for more information.");
                    }
                }
            }

            // allowed enum values (copy your enum values exactly)
            $allowedPurposes = [
                'Personal Celebration', 'Sports Activity', 'Barangay Escort',
                'Community Event', 'Religious or Cultural Activity', 'Logistical Support'
            ];

            // Validate required fields that are NOT NULL in the DB
            try {
                $validated = $request->validate([
                    'event_type' => ['required', 'string', 'max:255'],
                    'purpose' => ['nullable', 'string'], // No length limit - stored in LONGTEXT column
                    'others' => ['nullable', 'string'], // No length limit
                    'other_purpose' => ['nullable', 'string'], // No length limit - stored in TEXT column
                    'location' => 'nullable|string',
                    'event_location' => 'nullable|string',
                    'event_date' => ['required', 'date'],
                    'event_time' => ['nullable', 'string'],
                    'start_datetime' => ['nullable', 'string'],
                    'end_time' => ['nullable', 'string'],
                    'end_datetime' => ['nullable', 'string'],
                    'duration' => ['nullable', 'integer'],
                    'attendees' => ['nullable', 'integer', 'min:0'],
                    'expected_attendees' => ['nullable', 'integer', 'min:0'],
                    'manpower_count' => ['nullable', 'integer', 'min:1'],
                    'court_type' => ['nullable', 'string'],
                    'document_type' => ['nullable', 'string'],
                    'id_type' => ['nullable', 'string'],
                    'valid_id_number' => ['nullable', 'string', 'max:255'],
                    'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                    'id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                    'documents' => ['nullable'],
                    'documents.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                    'extra_fields' => ['nullable', 'array'],
                    'extra_fields.*' => ['nullable'],
                    'valid_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                    'valid_id_content' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                ]);
            } catch (\Illuminate\Validation\ValidationException $ve) {
                \Log::error('Event Assistance validation failed:', [
                    'errors' => $ve->errors(),
                    'input' => $request->except(['valid_id_content', 'id_front', 'id_back', 'documents']),
                ]);
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $ve->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $userId = $user->user_id ?? $user->id ?? Auth::id();

            // Get event type and location
            $eventType = $request->input('event_type', '');
            $eventLocation = $request->input('location') ?? $request->input('event_location') ?? '';
            
            // Get purpose - use 'purpose' field or 'others' if purpose is 'Other'
            $purpose = $request->input('purpose');
            $otherPurpose = $request->input('others') ?? $request->input('other_purpose') ?? null;
            
            // otherPurpose can be any length - stored in other_purpose column (TEXT type)
            
            // If purpose is "Other", use the others field, otherwise use purpose or event_type as fallback
            if ($purpose === 'Other') {
                $purpose = $otherPurpose ?? $eventType ?? 'Event Assistance';
            } elseif (empty($purpose)) {
                // If no purpose provided, use event_type as fallback
                $purpose = $eventType ?? 'Event Assistance';
            }
            
            // Ensure purpose is not empty
            if (empty($purpose)) {
                $purpose = $eventType ?? 'Event Assistance';
            }
            
            // Purpose field is now LONGTEXT in database, so no truncation needed
            // Log purpose value for debugging
            \Log::info('Event Assistance - Purpose value:', [
                'purpose' => $purpose,
                'purpose_length' => strlen($purpose),
                'event_type' => $eventType,
                'other_purpose' => $otherPurpose,
            ]);

            // Get attendees
            $attendees = $request->input('attendees') ?? $request->input('expected_attendees') ?? 0;
            
            // Resolve valid_id_type_id from id_type string (if provided)
            // The column doesn't allow NULL, so we MUST have a valid ID
            $fkValidIdType = null;
            $idTypeString = $request->input('id_type');
            
            // Try to resolve valid_id_type_id from the valid_id_types table
            try {
                if (!empty($idTypeString)) {
                    // Try to find matching valid_id_type by name
                    $validIdType = DB::table('valid_id_types')
                        ->where(function($query) use ($idTypeString) {
                            // Try common column name variations
                            $query->where('id_type_name', $idTypeString)
                                  ->orWhere('name', $idTypeString)
                                  ->orWhere('type_name', $idTypeString)
                                  ->orWhere('id_type', $idTypeString);
                        })
                        ->first();
                    
                    if ($validIdType) {
                        $fkValidIdType = $validIdType->valid_id_type_id ?? $validIdType->id ?? null;
                        \Log::info('Found valid_id_type_id from id_type string:', [
                            'id_type_string' => $idTypeString,
                            'valid_id_type_id' => $fkValidIdType,
                        ]);
                    }
                }
                
                // If still null, MUST get the first valid_id_type as fallback (column doesn't allow NULL)
                if ($fkValidIdType === null) {
                    $firstValidIdType = DB::table('valid_id_types')->first();
                    if ($firstValidIdType) {
                        $fkValidIdType = $firstValidIdType->valid_id_type_id ?? $firstValidIdType->id ?? null;
                        \Log::info('Using first available valid_id_type_id as fallback:', [
                            'valid_id_type_id' => $fkValidIdType,
                        ]);
                    } else {
                        // No valid_id_types in database - this is a problem
                        \Log::error('No valid_id_types found in database - cannot create event assistance request');
                        throw new \Exception('No valid ID types configured in the system. Please contact administrator.');
                    }
                }
                
                // Final check - if still null, we have a problem
                if ($fkValidIdType === null) {
                    \Log::error('Could not resolve fk_valid_id_type - column does not allow NULL');
                    throw new \Exception('Unable to determine valid ID type. Please try again or contact support.');
                }
            } catch (\Exception $e) {
                // If it's our custom exception, re-throw it
                if (strpos($e->getMessage(), 'No valid ID types') !== false || 
                    strpos($e->getMessage(), 'Unable to determine') !== false) {
                    throw $e;
                }
                \Log::error('Could not resolve valid_id_type_id: ' . $e->getMessage());
                // Try one more time to get ANY valid_id_type
                try {
                    $anyValidIdType = DB::table('valid_id_types')->first();
                    if ($anyValidIdType) {
                        $fkValidIdType = $anyValidIdType->valid_id_type_id ?? $anyValidIdType->id ?? null;
                    } else {
                        throw new \Exception('No valid ID types configured in the system. Please contact administrator.');
                    }
                } catch (\Exception $e2) {
                    throw new \Exception('Database error: Unable to resolve valid ID type. Please contact administrator.');
                }
            }
            
            \Log::info('Final fk_valid_id_type value:', [
                'id_type_string' => $idTypeString,
                'fk_valid_id_type' => $fkValidIdType,
            ]);

            // Get event date and time
            $eventDate = $request->input('event_date');
            $eventTime = $request->input('event_time') ?? $request->input('event_start') ?? '';
            $endTime = $request->input('end_time') ?? $request->input('event_end') ?? '';

            // Compute event_start and event_end from date + time
            $eventStart = null;
            $eventEnd = null;
            
            if ($eventDate && $eventTime) {
                try {
                    $eventStart = Carbon::parse($eventDate . ' ' . $eventTime)->format('H:i:s');
                } catch (\Throwable $e) {
                    $eventStart = $eventTime;
                }
            } elseif ($eventTime) {
                $eventStart = $eventTime;
            }

            if ($eventDate && $endTime) {
                try {
                    $eventEnd = Carbon::parse($eventDate . ' ' . $endTime)->format('H:i:s');
                } catch (\Throwable $e) {
                    $eventEnd = $endTime;
                }
            } elseif ($endTime) {
                $eventEnd = $endTime;
            } elseif ($eventStart) {
                // Default to 1 hour after start if no end time provided
                try {
                    $startDt = Carbon::parse($eventDate . ' ' . $eventStart);
                    $endDt = $startDt->copy()->addHour();
                    $eventEnd = $endDt->format('H:i:s');
                } catch (\Throwable $e) {
                    $eventEnd = '00:00:00';
                }
            }

            // Handle valid_id_content file -> read raw bytes and keep as binary (like document requests)
            // Priority: 'valid_id_content' (sent by frontend) -> 'id_front' -> 'id_back'
            $validIdContentBinary = null;
            
            if ($request->hasFile('valid_id_content')) {
                $uploaded = $request->file('valid_id_content');
                if ($uploaded && $uploaded->isValid()) {
                    // Read as binary - do NOT treat as UTF-8 string
                    $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
                }
            } elseif ($request->hasFile('id_front')) {
                $uploaded = $request->file('id_front');
                if ($uploaded && $uploaded->isValid()) {
                    $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
                }
            } elseif ($request->hasFile('id_back')) {
                $uploaded = $request->file('id_back');
                if ($uploaded && $uploaded->isValid()) {
                    $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
                }
            } elseif ($request->hasFile('valid_id')) {
                $uploaded = $request->file('valid_id');
                if ($uploaded && $uploaded->isValid()) {
                    $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
                }
            }


            // Process extra_fields: separate files from text values (same pattern as document requests)
            $extraFieldsData = [];
            $fileAttachments = [];
            
            // Get all files from the request
            $allFiles = $request->allFiles();
            
            \Log::info('Event Assistance - All files received:', [
                'file_keys' => array_keys($allFiles),
                'file_count' => count($allFiles),
            ]);
            
            // Check for files in extra_fields structure
            foreach ($allFiles as $key => $file) {
                // Handle extra_fields as an array of files (nested structure)
                if ($key === 'extra_fields' && is_array($file)) {
                    foreach ($file as $fieldName => $fieldFile) {
                        if ($fieldFile instanceof \Illuminate\Http\UploadedFile) {
                            $fileAttachments[$fieldName] = $fieldFile;
                        } elseif (is_array($fieldFile)) {
                            foreach ($fieldFile as $idx => $f) {
                                if ($f instanceof \Illuminate\Http\UploadedFile) {
                                    $fileAttachments[$fieldName . '_' . $idx] = $f;
                                }
                            }
                        }
                    }
                }
                // Check if this is an extra_fields file: "extra_fields[fieldName]"
                elseif (preg_match('/^extra_fields\[(.+)\]$/', $key, $matches)) {
                    $fieldName = $matches[1];
                    $fileAttachments[$fieldName] = is_array($file) ? $file[0] : $file;
                } elseif (strpos($key, 'extra_fields_') === 0) {
                    $fieldName = substr($key, 13);
                    $fileAttachments[$fieldName] = is_array($file) ? $file[0] : $file;
                }
            }
            
            // Process text values from extra_fields
            $extraFields = $request->input('extra_fields', []);
            if (is_array($extraFields)) {
                foreach ($extraFields as $fieldName => $fieldValue) {
                    // Skip if this field is a file (already handled above)
                    if (isset($fileAttachments[$fieldName])) {
                        continue;
                    }
                    
                    // Handle "Other" option for purpose_of_use - use other_purpose_of_use if purpose_of_use is "Other"
                    if ($fieldName === 'purpose_of_use' && $fieldValue === 'Other') {
                        $otherPurposeOfUse = $extraFields['other_purpose_of_use'] ?? null;
                        if (!empty($otherPurposeOfUse)) {
                            $extraFieldsData[$fieldName] = $otherPurposeOfUse;
                        } else {
                            $extraFieldsData[$fieldName] = 'Other';
                        }
                        continue; // Skip the normal processing for this field
                    }
                    
                    // It's a text value, store in extra_fields JSON
                    if (is_array($fieldValue)) {
                        if (!empty($fieldValue)) {
                            $extraFieldsData[$fieldName] = $fieldValue;
                        }
                    } elseif ($fieldValue !== null && $fieldValue !== '') {
                        $extraFieldsData[$fieldName] = $fieldValue;
                    }
                }
            }
            
            // IMPORTANT: Store event_type in extra_fields so it can be retrieved later
            // This is the selected assistance type (e.g., "Manpower Assistance", "Court Reservation")
            if (!empty($eventType)) {
                $extraFieldsData['event_type'] = $eventType;
            }

            // Generate sequential ticket number (like document requests: EA-001, EA-002, etc.)
            $ticket = null;
            $created = null;
            
            DB::transaction(function () use (&$created, &$ticket, $userId, $eventType, $eventDate, $eventStart, $eventEnd, $attendees, $purpose, $otherPurpose, $eventLocation, $validIdContentBinary, $extraFieldsData, &$fileAttachments, $request, $fkValidIdType) {
                // Generate sequential ticket number with lock
                $tickets = DB::table('event_assistance_requests')
                    ->whereRaw("event_assist_request_ticket REGEXP 'EA-[0-9]+'")
                    ->lockForUpdate()
                    ->pluck('event_assist_request_ticket');

                $maxNum = 0;
                foreach ($tickets as $t) {
                    if (preg_match('/EA-(\d+)/', $t, $m)) {
                        $num = intval($m[1]);
                        if ($num > $maxNum) $maxNum = $num;
                    }
                }

                $next = $maxNum + 1;
                $ticket = 'EA-' . str_pad($next, 3, '0', STR_PAD_LEFT);

                // Build data array - we'll handle valid_id_content separately using raw SQL
                $data = [
                    'event_assist_request_ticket' => $ticket,
                    'fk_user_id' => $userId,
                    'fk_approver_id' => null,
                    'event_date' => $eventDate,
                    'event_start' => $eventStart ?? '00:00:00',
                    'event_end' => $eventEnd ?? '00:00:00',
                    'expected_attendees' => $attendees,
                    'purpose' => $purpose ?? $eventType,
                    'other_purpose' => $otherPurpose,
                    'event_location' => $eventLocation,
                    'fk_valid_id_type' => $fkValidIdType, // Resolved from id_type or null
                    'extra_fields' => !empty($extraFieldsData) ? json_encode($extraFieldsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                    'status' => 'Pending',
                    'admin_feedback' => null,
                    'reviewed_at' => null,
                    'created_at' => now(),
                ];

                // Use raw SQL to insert with valid_id_content always included
                // This ensures the column is always present in the INSERT statement
                $pdo = DB::connection()->getPdo();
                
                $columns = array_keys($data);
                $columns[] = 'valid_id_content'; // Always include this column
                
                $placeholders = array_fill(0, count($columns), '?');
                $values = array_values($data);
                
                // Add valid_id_content value - use binary data if available, otherwise empty binary string
                if ($validIdContentBinary !== null && !empty($validIdContentBinary)) {
                    $values[] = $validIdContentBinary;
                } else {
                    // Use empty binary string since column doesn't allow NULL
                    $values[] = '';
                }
                
                $columnList = implode(', ', array_map(function($col) {
                    return "`$col`";
                }, $columns));
                
                $sql = "INSERT INTO event_assistance_requests ($columnList) VALUES (" . implode(', ', $placeholders) . ")";
                
                $stmt = $pdo->prepare($sql);
                
                // Bind all values - handle binary data for valid_id_content, LONGTEXT for purpose, and NULL for fk_valid_id_type
                foreach ($values as $index => $value) {
                    $paramIndex = $index + 1;
                    $columnName = $columns[$index];
                    
                    if ($columnName === 'valid_id_content') {
                        // Last value is valid_id_content - handle as binary
                        if ($validIdContentBinary !== null && !empty($validIdContentBinary)) {
                            $stmt->bindValue($paramIndex, $value, \PDO::PARAM_LOB);
                        } else {
                            // Empty string for LONGBLOB
                            $stmt->bindValue($paramIndex, $value, \PDO::PARAM_STR);
                        }
                    } elseif ($columnName === 'purpose') {
                        // Explicitly bind purpose as string to handle LONGTEXT properly
                        // Convert null to empty string if needed
                        $purposeValue = $value ?? '';
                        // Log the purpose value length before binding
                        \Log::info('Binding purpose value:', [
                            'length' => strlen($purposeValue),
                            'value_preview' => substr($purposeValue, 0, 100),
                        ]);
                        $stmt->bindValue($paramIndex, $purposeValue, \PDO::PARAM_STR);
                    } elseif ($columnName === 'fk_valid_id_type') {
                        // Handle fk_valid_id_type - column doesn't allow NULL, so we must have a value
                        // $fkValidIdType should never be null at this point (checked above)
                        if ($value === null || $value === '') {
                            \Log::error('fk_valid_id_type is null in SQL binding - this should not happen!');
                            throw new \Exception('Valid ID type is required but not provided.');
                        }
                        $stmt->bindValue($paramIndex, $value, \PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($paramIndex, $value);
                    }
                }
                
                // Execute the statement
                // Set error mode to exceptions only (suppress warnings)
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $stmt->execute();
                
                // Get the inserted ID
                $insertedId = $pdo->lastInsertId();
                $created = EventAssistanceRequest::find($insertedId);
                
                if (!$created || !$created->event_assist_request_id) {
                    throw new \Exception('Failed to create event assistance request - record not found after insert');
                }
                
                \Log::info('Event Assistance Request created successfully:', [
                    'id' => $created->event_assist_request_id,
                    'ticket' => $ticket,
                ]);

                // Save ID front and back as separate attachments if they exist (like document requests)
                // Only save if they're not already in fileAttachments (to avoid duplicates)
                if ($request->hasFile('id_front') && !isset($fileAttachments['id_front'])) {
                    $idFrontFile = $request->file('id_front');
                    if ($idFrontFile && $idFrontFile->isValid()) {
                        try {
                            $path = $idFrontFile->store('event_assistance_attachments', 'public');
                            if ($path) {
                                EventAssistanceAttachment::create([
                                    'fk_event_assist_request_id' => $created->event_assist_request_id,
                                    'field_name' => 'id_front',
                                    'file_name' => $idFrontFile->getClientOriginalName(),
                                    'file_path' => $path,
                                    'file_type' => $idFrontFile->getMimeType(),
                                    'file_size' => $idFrontFile->getSize(),
                                ]);
                                \Log::info('Saved ID front as attachment');
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to save ID front attachment: ' . $e->getMessage());
                        }
                    }
                }
                
                if ($request->hasFile('id_back') && !isset($fileAttachments['id_back'])) {
                    $idBackFile = $request->file('id_back');
                    if ($idBackFile && $idBackFile->isValid()) {
                        try {
                            $path = $idBackFile->store('event_assistance_attachments', 'public');
                            if ($path) {
                                EventAssistanceAttachment::create([
                                    'fk_event_assist_request_id' => $created->event_assist_request_id,
                                    'field_name' => 'id_back',
                                    'file_name' => $idBackFile->getClientOriginalName(),
                                    'file_path' => $path,
                                    'file_type' => $idBackFile->getMimeType(),
                                    'file_size' => $idBackFile->getSize(),
                                ]);
                                \Log::info('Saved ID back as attachment');
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to save ID back attachment: ' . $e->getMessage());
                        }
                    }
                }

                // Save file attachments from extra_fields
                if (!empty($fileAttachments) && is_array($fileAttachments)) {
                    foreach ($fileAttachments as $fieldName => $file) {
                        // Skip id_front and id_back as they're already handled above
                        if ($fieldName === 'id_front' || $fieldName === 'id_back') {
                            continue;
                        }
                        
                        if (!$file || !($file instanceof \Illuminate\Http\UploadedFile)) {
                            continue;
                        }
                        
                        if (!$file->isValid()) {
                            \Log::warning('Invalid file skipped:', ['field_name' => $fieldName]);
                            continue;
                        }
                        
                        try {
                            $path = $file->store('event_assistance_attachments', 'public');
                            
                            if (!$path) {
                                \Log::error('File storage returned empty path:', ['field_name' => $fieldName]);
                                continue;
                            }
                            
                            EventAssistanceAttachment::create([
                                'fk_event_assist_request_id' => $created->event_assist_request_id,
                                'field_name' => $fieldName,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to save attachment:', [
                                'field_name' => $fieldName,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                // Also save main documents array if provided
                if ($request->hasFile('documents')) {
                    $files = $request->file('documents');
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $f) {
                        if (!$f || !$f->isValid()) continue;
                        try {
                            $path = $f->store('event_assistance_attachments', 'public');
                            EventAssistanceAttachment::create([
                                'fk_event_assist_request_id' => $created->event_assist_request_id,
                                'field_name' => 'main_document',
                                'file_name' => $f->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $f->getMimeType(),
                                'file_size' => $f->getSize(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to save main document:', ['error' => $e->getMessage()]);
                        }
                    }
                }
            });

            \Log::info('Event Assistance Request - Transaction completed:', [
                'event_assist_request_id' => $created->event_assist_request_id ?? 'NOT SET',
                'ticket' => $ticket ?? 'NOT SET',
            ]);

            if (!$created || !$created->event_assist_request_id) {
                \Log::error('Event Assistance Request creation failed - no ID returned');
                return response()->json([
                    'error' => 'Failed to create event assistance request',
                    'message' => 'The request could not be saved. Please try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'ticket' => $ticket,
                'id' => $created->event_assist_request_id,
            ], 201);
        } catch (\Throwable $ex) {
            \Log::error('Event Assistance store error: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
            ]);
            
            // Return detailed error in debug mode, generic message otherwise
            $msg = config('app.debug') 
                ? $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine()
                : 'Internal server error. Please try again.';
            
            return response()->json([
                'message' => $msg,
                'error' => config('app.debug') ? $ex->getMessage() : 'An error occurred'
            ], 500);
        }
    }


    /**
     * List requests for approver / UI
     */
    public function index(Request $request)
    {
        // If this is an API request (JSON), return JSON response
        if ($request->wantsJson() || $request->expectsJson()) {
            return $this->indexJson($request);
        }

        // Eager load the request items (new model), user credential, and attachments
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $requests = EventAssistanceRequest::with(['requestItems', 'user', 'user.credential', 'attachments'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Create a lookup of available item names to avoid N+1 queries later
        $availableItemsMap = EventAssistanceAvailableItem::pluck('item_name', 'event_assist_item_id')->toArray();

        $mapped = $requests->map(function ($r) use ($availableItemsMap) {
            $user = $r->user;
            $cred = $user?->credential;

            // Name normalization (like document requests)
            $rawFirst  = trim((string) ($user?->first_name ?? ''));
            $rawMiddle = trim((string) ($user?->middle_name ?? ''));
            $rawLast   = trim((string) ($user?->last_name ?? ''));
            $rawSuffix = trim((string) ($user?->suffix ?? ''));

            $normalize = function ($s) {
                $s = trim((string) $s);
                if ($s === '') return '';
                $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                $words = array_map(function ($w) {
                    $w = mb_strtolower($w);
                    $first = mb_strtoupper(mb_substr($w, 0, 1));
                    $rest  = mb_substr($w, 1);
                    return $first . $rest;
                }, $words);
                return implode(' ', $words);
            };

            $first  = $normalize($rawFirst);
            $middle = $normalize($rawMiddle);
            $last   = $normalize($rawLast);
            $suffix = $normalize($rawSuffix);

            $nameParts = array_filter([$first, $middle, $last, $suffix], function ($p) {
                return $p !== '' && $p !== null;
            });
            $displayName = implode(' ', $nameParts) ?: ($user?->name ?? 'Unknown');

            $eventDate = $r->event_date ? Carbon::parse($r->event_date)->format('F j, Y') : null;

            $eventTime = null;
            if ($r->event_start && $r->event_end) {
                try {
                    $start = Carbon::parse($r->event_start);
                    $end = Carbon::parse($r->event_end);
                    $eventTime = $start->format('g:i A') . ' - ' . $end->format('g:i A');
                } catch (\Throwable $ex) {
                    $eventTime = "{$r->event_start} - {$r->event_end}";
                }
            } elseif ($r->event_start) {
                try {
                    $start = Carbon::parse($r->event_start);
                    $eventTime = $start->format('g:i A');
                } catch (\Throwable $ex) {
                    $eventTime = $r->event_start;
                }
            }

            // prefer new relation name 'requestItems', fall back to old 'details' if present
            $itemsCollection = $r->requestItems ?? $r->details ?? collect();

            $assistanceDetails = null;
            if ($itemsCollection && $itemsCollection->count()) {
                $assistanceDetails = $itemsCollection->map(function ($d) use ($availableItemsMap) {
                    $parts = [];

                    $fkItemId = $d->fk_event_assist_item_id ?? $d->fk_item_id ?? null;
                    $itemName = $fkItemId ? ($availableItemsMap[$fkItemId] ?? null) : null;

                    if (!empty($itemName)) {
                        $parts[] = $itemName;
                    } elseif (!empty($d->item)) {
                        $parts[] = $d->item;
                    } elseif (!empty($d->name)) {
                        $parts[] = $d->name;
                    }

                    if (!empty($d->description)) $parts[] = $d->description;
                    if (!empty($d->quantity)) $parts[] = 'x' . $d->quantity;

                    return trim(implode(' ', $parts));
                })->filter()->join(', ');
            }

            // Process attachments - use file_url accessor for consistent URLs
            $attachments = [];
            if ($r->attachments && $r->attachments->count() > 0) {
                $attachments = $r->attachments->map(function ($attachment) {
                    return [
                        'attachment_id' => $attachment->attachment_id,
                        'field_name' => $attachment->field_name,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'file_url' => Storage::url($attachment->file_path),
                        'created_at' => $attachment->created_at?->toDateTimeString(),
                    ];
                })->toArray();
            }

            // Get extra_fields (dynamic fields)
            $extraFields = $r->extra_fields ?? [];

            // Get user registration information
            $userInfo = $user ? [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'house_number' => $user->house_number,
                'street' => $user->street,
                'phase' => $user->phase,
                'package' => $user->package,
                'barangay' => $user->barangay,
                'city' => $user->city,
                'province' => $user->province,
                'zip_code' => $user->zip_code,
                'profile_pic' => $user->profile_pic,
                'fk_role_id' => $user->fk_role_id,
                'role_id' => $user->role_id,
            ] : null;

            // Get credential information
            $credentialInfo = $cred ? [
                'contact_number' => $cred->contact_number,
                'secondary_contact_number' => $cred->secondary_contact_number,
            ] : null;

            // Build profile image URL
            $profileImgUrl = '/assets/DEFAULT.jpg';
            if ($userInfo && isset($userInfo['profile_pic']) && $userInfo['profile_pic']) {
                $profilePic = $userInfo['profile_pic'];
                if (str_starts_with($profilePic, 'http://') || str_starts_with($profilePic, 'https://')) {
                    $profileImgUrl = $profilePic;
                } else {
                    $profileImgUrl = '/storage/' . ltrim($profilePic, '/');
                }
            }

            return [
                'id' => $r->event_assist_request_id,
                'referenceCode' => $r->event_assist_request_ticket ?? null,
                'name' => $displayName,
                'first_name' => $first,
                'middle_name' => $middle,
                'last_name' => $last,
                'suffix' => $suffix,
                'profileImg' => $profileImgUrl,
                'assistanceType' => $r->purpose ?? $r->event_location ?? null,
                'event_type' => $extraFields['event_type'] ?? null,
                'date' => $r->created_at ? Carbon::parse($r->created_at)->format('m/d/Y') : null,
                'dateObj' => $r->created_at ? Carbon::parse($r->created_at)->toDateTimeString() : ($r->event_date ?? null),
                'eventName' => $r->purpose ?? null,
                'eventDescription' => $r->other_purpose ?? null,
                'eventDate' => $eventDate,
                'eventTime' => $eventTime,
                'expectedAttendees' => $r->expected_attendees ?? null,
                'contact' => $cred?->contact_number ?? $user?->contact_number ?? null,
                'venue' => $r->event_location ?? null,
                'assistanceDetails' => $assistanceDetails,
                'age' => $user?->birthdate ? Carbon::parse($user->birthdate)->age : null,
                'sex' => $user?->sex ?? null,
                'civilStatus' => $user?->civil_status ?? null,
                'address' => $user?->address ?? ($userInfo ? ($userInfo['house_number'] . ' ' . ($userInfo['street'] ?? '')) : null),
                'birthdate' => $user?->birthdate ? Carbon::parse($user->birthdate)->format('m/d/Y') : null,
                'status' => $r->status ?? null,
                'event_start' => $r->event_start,
                'event_end' => $r->event_end,
                'created_at' => $r->created_at,
                'hasValidId' => !empty($r->valid_id_content),
                'validIdUrl' => url("/approver/event-requests/{$r->event_assist_request_id}/valid-id"),
                'valid_id_type' => $r->fk_valid_id_type,
                'valid_id_number' => null, // Add if column exists
                'attachments' => $attachments,
                'extra_fields' => $extraFields,
                'user_info' => $userInfo,
                'credential_info' => $credentialInfo,
            ];
        });

        // Provide available items to the front-end. Map to the expected keys used by the UI:
        $items = EventAssistanceAvailableItem::orderBy('item_name')->get()->map(function ($i) {
            return [
                // original code returned ['item_id', 'item_name', 'category']
                'item_id' => $i->event_assist_item_id,
                'item_name' => $i->item_name,
                // available items don't have a category column in the supplied model — keep null for compatibility
                'category' => $i->available_quantity ?? null,
            ];
        });

        return Inertia::render('Admin/Approver/A_Event_Request', [
            'requests' => $mapped,
            'items' => $items,
        ]);
    }

    /**
     * Serve the valid_id_content stored in the DB (LONGBLOB) or fallback to stored paths.
     *
     * URL: GET /approver/event-requests/{id}/valid-id
     */
    public function validId($id)
    {
        $r = EventAssistanceRequest::findOrFail($id);

        // Nothing uploaded
        if (empty($r->valid_id_content)) {
            abort(404, 'No valid id found for this request.');
        }

        $content = $r->valid_id_content;

        // If DB contains JSON array of storage paths (old behavior), return the first storage file
        $maybePaths = null;
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                $maybePaths = $decoded;
            }
        }

        if ($maybePaths && (!empty($maybePaths[0]))) {
            $path = $maybePaths[0];
            // storage path was saved with Storage::putFileAs("public/...", ...) so use Storage::exists()
            if (Storage::exists($path)) {
                // Storage::response will send correct headers and allow inline display for supported types
                return Storage::response($path);
            } else {
                abort(404, 'Stored file not found.');
            }
        }

        // Otherwise treat valid_id_content as raw binary (LONGBLOB)
        $binary = $content; // Eloquent returns string containing binary bytes

        // Attempt to detect mime type from binary
        $mime = 'application/octet-stream';
        try {
            if (function_exists('finfo_buffer')) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $detected = $finfo->buffer($binary);
                if ($detected) $mime = $detected;
            }
        } catch (\Throwable $ex) {
            // swallow and fallback to default
            \Log::warning('MIME detection failed for valid_id blob: ' . $ex->getMessage());
        }

        // create inline display for common displayable types (image/pdf); otherwise the browser will decide
        $fileName = 'valid_id_' . ($r->event_assist_request_ticket ?? $r->event_assist_request_id);

        return response($binary, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Length', strlen($binary))
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }


    /**
     * Approve a request and optionally create detail rows (now uses RequestItem model)
     */
    public function approve(Request $request, $id)
    {
        \Log::info('=== EVENT ASSISTANCE APPROVE METHOD CALLED ===', [
            'event_assist_request_id' => $id,
            'timestamp' => now()->toDateTimeString(),
        ]);

        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $requestModel = EventAssistanceRequest::with(['user', 'user.credential'])->findOrFail($id);

            $requestModel->status = 'Approved';
            $requestModel->fk_approver_id = $request->user()->user_id ?? $request->user()->id ?? Auth::id();
            $requestModel->reviewed_at = now();
            $requestModel->admin_feedback = $data['comment'];
            $requestModel->save();
            
            // Create notification for the user
            $userId = $requestModel->fk_user_id;
            $ticket = $requestModel->event_assist_request_ticket ?? 'N/A';
            $notificationMessage = "Your event assistance request (Ticket: {$ticket}) has been APPROVED.";
            if (!empty($data['comment'])) {
                $notificationMessage .= " " . substr($data['comment'], 0, 100);
            }
            
            if ($userId) {
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'EventAssistance',
                    'notification_reference_id' => $requestModel->event_assist_request_id,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            \Log::info('Transaction committed, preparing SMS notification', [
                'event_assist_request_id' => $id,
            ]);

            // Send SMS notification for approval
            try {
                $user = $requestModel->user;
                $credential = $user?->credential;
                $phoneNumber = $credential?->contact_number ?? $credential?->secondary_contact_number ?? null;
                
                \Log::info('Event assistance approval SMS attempt', [
                    'event_assist_request_id' => $id,
                    'has_user' => !empty($user),
                    'has_credential' => !empty($credential),
                    'has_phone' => !empty($phoneNumber),
                    'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
                ]);
                
                if ($phoneNumber && $user) {
                    $fullName = trim(implode(' ', array_filter([
                        $user->first_name ?? '',
                        $user->middle_name ?? '',
                        $user->last_name ?? '',
                        $user->suffix ?? '',
                    ]))) ?: 'User';
                    
                    $ticket = $requestModel->event_assist_request_ticket ?? 'N/A';
                    $message = "Hello {$fullName}, your event assistance request (Ticket: {$ticket}) has been APPROVED.";
                    if (!empty($data['comment'])) {
                        $message .= " " . substr($data['comment'], 0, 100);
                    }
                    $message .= " Thank you!";
                    
                    \Log::info('Calling SMS service for event assistance approval', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message, // Log full message for debugging
                    ]);
                    
                    $smsService = new OtpService();
                    \Log::info('About to call sendSms method for event assistance approval', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned for event assistance approval', [
                        'result' => $smsResult,
                    ]);
                    
                    \Log::info('SMS service result for event assistance approval', [
                        'success' => $smsResult['success'] ?? false,
                        'message' => $smsResult['message'] ?? 'No message',
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send approval SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                            'event_assist_request_id' => $id,
                        ]);
                    }
                } else {
                    \Log::warning('No phone number or user available for event assistance approval SMS', [
                        'event_assist_request_id' => $id,
                        'has_user' => !empty($user),
                        'has_phone' => !empty($phoneNumber),
                    ]);
                }
            } catch (\Throwable $smsEx) {
                // Don't fail the approval if SMS fails
                \Log::error('SMS notification error during approval', [
                    'error' => $smsEx->getMessage(),
                    'trace' => $smsEx->getTraceAsString(),
                    'event_assist_request_id' => $id,
                ]);
            }

            return redirect()->back()->with('success', 'Request approved.');
        } catch (\Throwable $ex) {
            DB::rollBack();
            \Log::error('Approve exception: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to approve the request.');
        }
    }

    public function reject(Request $request, $id)
    {
        $data = $request->validate([
            'reason' => ['required', 'string']
        ]);

        try {
            $requestModel = EventAssistanceRequest::with(['user', 'user.credential'])->findOrFail($id);
            $requestModel->status = 'Rejected';
            $requestModel->fk_approver_id = $request->user()->user_id ?? $request->user()->id ?? Auth::id();
            $requestModel->reviewed_at = now();
            if (property_exists($requestModel, 'admin_feedback') || \Schema::hasColumn('event_assistance_requests', 'admin_feedback')) {
                $requestModel->admin_feedback = $data['reason'];
            }
            $requestModel->save();
            
            // Create notification for the user
            $userId = $requestModel->fk_user_id;
            $ticket = $requestModel->event_assist_request_ticket ?? 'N/A';
            $notificationMessage = "Your event assistance request (Ticket: {$ticket}) has been REJECTED.";
            if (!empty($data['reason'])) {
                $notificationMessage .= " Reason: " . substr($data['reason'], 0, 100);
            }
            $notificationMessage .= " You can fix your uploaded request fields and resubmit your request through the website.";
            
            if ($userId) {
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'EventAssistance',
                    'notification_reference_id' => $requestModel->event_assist_request_id,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }

            // Send SMS notification for rejection
            try {
                $user = $requestModel->user;
                $credential = $user?->credential;
                $phoneNumber = $credential?->contact_number ?? $credential?->secondary_contact_number ?? null;
                
                \Log::info('Event assistance rejection SMS attempt', [
                    'event_assist_request_id' => $id,
                    'has_user' => !empty($user),
                    'has_credential' => !empty($credential),
                    'has_phone' => !empty($phoneNumber),
                    'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
                ]);
                
                if ($phoneNumber && $user) {
                    $fullName = trim(implode(' ', array_filter([
                        $user->first_name ?? '',
                        $user->middle_name ?? '',
                        $user->last_name ?? '',
                        $user->suffix ?? '',
                    ]))) ?: 'User';
                    
                    $ticket = $requestModel->event_assist_request_ticket ?? 'N/A';
                    $message = "Hello {$fullName}, your event assistance request (Ticket: {$ticket}) has been REJECTED.";
                    if (!empty($data['reason'])) {
                        $message .= " Reason: " . substr($data['reason'], 0, 100);
                    }
                    $message .= " You can fix your uploaded request fields and resubmit your request through the website. Please contact the barangay office for more information. Thank you.";
                    
                    \Log::info('Calling SMS service for event assistance rejection', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message, // Log full message for debugging
                    ]);
                    
                    $smsService = new OtpService();
                    \Log::info('About to call sendSms method for event assistance rejection', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned for event assistance rejection', [
                        'result' => $smsResult,
                    ]);
                    
                    \Log::info('SMS service result for event assistance rejection', [
                        'success' => $smsResult['success'] ?? false,
                        'message' => $smsResult['message'] ?? 'No message',
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send rejection SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                            'event_assist_request_id' => $id,
                        ]);
                    }
                } else {
                    \Log::warning('No phone number or user available for event assistance rejection SMS', [
                        'event_assist_request_id' => $id,
                        'has_user' => !empty($user),
                        'has_phone' => !empty($phoneNumber),
                    ]);
                }
            } catch (\Throwable $smsEx) {
                // Don't fail the rejection if SMS fails
                \Log::error('SMS notification error during rejection', [
                    'error' => $smsEx->getMessage(),
                    'trace' => $smsEx->getTraceAsString(),
                    'event_assist_request_id' => $id,
                ]);
            }

            return redirect()->back()->with('success', 'Request rejected.');
        } catch (\Throwable $ex) {
            \Log::error('Reject exception: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to reject the request.');
        }
    }

    /**
     * JSON API endpoint for history/filtering (similar to DocumentRequestController::docuReqJson)
     */
    private function indexJson(Request $request)
    {
        $authUser = $request->user();
        $allowedRoles = [3, 9]; // approver role id (3) and system admin role id (9)
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;

        if (! in_array($userRole, $allowedRoles, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requestedStatus = $request->query('status', 'Pending');
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $query = EventAssistanceRequest::with(['requestItems', 'user', 'user.credential', 'attachments', 'approver'])
            ->orderBy('created_at', 'desc');

        if (is_string($requestedStatus) && strtolower($requestedStatus) !== 'all') {
            $statusFilter = ucfirst(strtolower($requestedStatus));
            $query->where('status', $statusFilter);
        }

        $rows = $query->get();

        $eventRequests = $rows->map(function ($r) {
            $user = $r->user;
            $cred = $user?->credential;

            // Name normalization (same as index method)
            $rawFirst  = trim((string) ($user?->first_name ?? ''));
            $rawMiddle = trim((string) ($user?->middle_name ?? ''));
            $rawLast   = trim((string) ($user?->last_name ?? ''));
            $rawSuffix = trim((string) ($user?->suffix ?? ''));

            $normalize = function ($s) {
                $s = trim((string) $s);
                if ($s === '') return '';
                $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                $words = array_map(function ($w) {
                    $w = mb_strtolower($w);
                    $first = mb_strtoupper(mb_substr($w, 0, 1));
                    $rest  = mb_substr($w, 1);
                    return $first . $rest;
                }, $words);
                return implode(' ', $words);
            };

            $first  = $normalize($rawFirst);
            $middle = $normalize($rawMiddle);
            $last   = $normalize($rawLast);
            $suffix = $normalize($rawSuffix);

            $nameParts = array_filter([$first, $middle, $last, $suffix], function ($p) {
                return $p !== '' && $p !== null;
            });
            $displayName = implode(' ', $nameParts) ?: ($user?->name ?? 'Unknown');

            // Get event type from extra_fields or purpose
            $extraFields = $r->extra_fields ?? [];
            if (is_string($extraFields)) {
                try {
                    $decoded = json_decode($extraFields, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $extraFields = $decoded;
                    }
                } catch (\Exception $e) {
                    $extraFields = [];
                }
            }
            $eventType = $extraFields['event_type'] ?? $r->purpose ?? 'Event Assistance Request';

            $userInfo = [
                'user_id' => $user?->user_id,
                'email' => $user?->email,
                'house_number' => $user?->house_number,
                'street' => $user?->street,
                'phase' => $user?->phase,
                'package' => $user?->package,
                'barangay' => $user?->barangay,
                'city' => $user?->city,
                'province' => $user?->province,
                'zip_code' => $user?->zip_code,
                'profile_pic' => $user?->profile_pic,
                'fk_role_id' => $user?->fk_role_id,
                'role_id' => $user?->role_id,
            ];

            $credentialInfo = $cred ? [
                'contact_number' => $cred->contact_number,
                'secondary_contact_number' => $cred->secondary_contact_number,
            ] : null;

            // Get approver information
            $approver = $r->approver;
            $approverInfo = null;
            if ($approver) {
                $approverFirst = trim((string) ($approver->first_name ?? ''));
                $approverLast = trim((string) ($approver->last_name ?? ''));
                
                $normalizeApprover = function ($s) {
                    $s = trim((string) $s);
                    if ($s === '') return '';
                    $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                    $words = array_map(function ($w) {
                        $w = mb_strtolower($w);
                        $first = mb_strtoupper(mb_substr($w, 0, 1));
                        $rest  = mb_substr($w, 1);
                        return $first . $rest;
                    }, $words);
                    return implode(' ', $words);
                };
                
                $approverFirst = $normalizeApprover($approverFirst);
                $approverLast = $normalizeApprover($approverLast);
                
                // Format as "First L." or just "First" if no last name
                $approverDisplayName = $approverFirst;
                if ($approverLast) {
                    $approverDisplayName .= ' ' . mb_substr($approverLast, 0, 1) . '.';
                }
                
                $approverInfo = [
                    'user_id' => $approver->user_id,
                    'name' => $approverDisplayName,
                    'first_name' => $approverFirst,
                    'last_name' => $approverLast,
                ];
            }

            return [
                'event_assist_request_id' => $r->event_assist_request_id,
                'event_assist_request_ticket' => $r->event_assist_request_ticket,
                'fk_user_id' => $r->fk_user_id,
                'last_name' => $last,
                'first_name' => $first,
                'middle_name' => $middle,
                'suffix' => $suffix,
                'name' => $displayName,
                'event_type' => $eventType,
                'purpose' => $r->purpose,
                'event_date' => $r->event_date,
                'event_start' => $r->event_start,
                'event_end' => $r->event_end,
                'event_location' => $r->event_location,
                'expected_attendees' => $r->expected_attendees,
                'status' => $r->status,
                'fk_approver_id' => $r->fk_approver_id,
                'reviewed_at' => $r->reviewed_at,
                'admin_feedback' => $r->admin_feedback,
                'created_at' => $r->created_at?->toDateTimeString(),
                'user_info' => $userInfo,
                'credential_info' => $credentialInfo,
                'approver_info' => $approverInfo,
            ];
        });

        return response()->json([
            'event_requests' => $eventRequests,
        ]);
    }

    /**
     * Show appeal form for rejected request
     * Automatically resets status to Pending when accessed
     */
    public function appealForm(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser) {
            return redirect()->route('login');
        }

        $eventRequest = EventAssistanceRequest::with(['requestItems', 'attachments', 'user'])
            ->where('event_assist_request_id', $id)
            ->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($eventRequest->fk_user_id != $userId) {
            abort(403, 'Unauthorized');
        }

        // Check if request is rejected
        if (strtoupper($eventRequest->status) !== 'REJECTED') {
            return redirect()->back()->with('error', 'Only rejected requests can be appealed.');
        }

        // Automatically reset status to Pending when accessing appeal form
        $eventRequest->status = 'Pending';
        $eventRequest->fk_approver_id = null;
        $eventRequest->reviewed_at = null;
        $eventRequest->save();

        // Get valid ID type name if exists
        $validIdTypeName = null;
        if ($eventRequest->fk_valid_id_type) {
            $validIdType = DB::table('valid_id_types')
                ->where('valid_id_type_id', $eventRequest->fk_valid_id_type)
                ->first();
            $validIdTypeName = $validIdType->id_type_name ?? $validIdType->name ?? $validIdType->type_name ?? null;
        }

        // Get extra_fields
        $extraFields = $eventRequest->extra_fields ?? [];
        if (is_string($extraFields)) {
            try {
                $decoded = json_decode($extraFields, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $extraFields = $decoded;
                }
            } catch (\Exception $e) {
                $extraFields = [];
            }
        }

        // Prepare request data for editing
        $requestData = [
            'event_assist_request_id' => $eventRequest->event_assist_request_id,
            'event_assist_request_ticket' => $eventRequest->event_assist_request_ticket,
            'event_type' => $extraFields['event_type'] ?? null,
            'purpose' => $eventRequest->purpose,
            'other_purpose' => $eventRequest->other_purpose,
            'event_date' => $eventRequest->event_date ? Carbon::parse($eventRequest->event_date)->format('Y-m-d') : null,
            'event_start' => $eventRequest->event_start,
            'event_end' => $eventRequest->event_end,
            'expected_attendees' => $eventRequest->expected_attendees,
            'event_location' => $eventRequest->event_location,
            'id_type' => $validIdTypeName,
            'fk_valid_id_type' => $eventRequest->fk_valid_id_type,
            'valid_id_number' => null, // Add if column exists
            'extra_fields' => $extraFields,
            'attachments' => $eventRequest->attachments->map(function ($att) {
                return [
                    'id' => $att->attachment_id,
                    'field_name' => $att->field_name,
                    'file_name' => $att->file_name,
                    'file_path' => $att->file_path,
                    'file_type' => $att->file_type,
                    'url' => $att->file_path ? Storage::url($att->file_path) : null,
                ];
            })->toArray(),
            'request_items' => $eventRequest->requestItems->map(function ($item) {
                return [
                    'fk_event_assist_item_id' => $item->fk_event_assist_item_id ?? $item->fk_item_id ?? null,
                    'quantity' => $item->quantity ?? null,
                    'description' => $item->description ?? null,
                ];
            })->toArray(),
            'admin_feedback' => $eventRequest->admin_feedback,
        ];

        \Log::info('Event assistance appeal form accessed, status reset to Pending', [
            'event_assist_request_id' => $id,
            'user_id' => $userId,
        ]);

        return Inertia::render('User/Resident/R_Event_Assistance_Appeal', [
            'requestData' => $requestData,
        ]);
    }

    /**
     * Handle appeal submission - reset status to Pending and allow editing
     */
    public function appeal(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $eventRequest = EventAssistanceRequest::where('event_assist_request_id', $id)->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($eventRequest->fk_user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request is rejected
        if (strtoupper($eventRequest->status) !== 'REJECTED') {
            return response()->json(['error' => 'Only rejected requests can be appealed.'], 400);
        }

        try {
            // Reset status to Pending and clear review information
            $eventRequest->status = 'Pending';
            $eventRequest->fk_approver_id = null;
            $eventRequest->reviewed_at = null;
            // Keep admin_feedback for reference so user can see what was wrong
            $eventRequest->save();

            \Log::info('Event assistance request appealed', [
                'event_assist_request_id' => $id,
                'user_id' => $userId,
                'ticket' => $eventRequest->event_assist_request_ticket,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request has been reset to pending. You can now edit and resubmit it.',
                'data' => [
                    'event_assist_request_id' => $eventRequest->event_assist_request_id,
                    'status' => $eventRequest->status,
                ],
            ]);
        } catch (\Throwable $ex) {
            \Log::error('Event assistance appeal exception: ' . $ex->getMessage());
            return response()->json(['error' => 'Failed to appeal the request.'], 500);
        }
    }

    /**
     * Update an existing event assistance request (for appeals/resubmissions)
     */
    public function update(Request $request, $id)
    {
        \Log::info('=== EVENT ASSISTANCE REQUEST UPDATE CALLED ===', [
            'event_assist_request_id' => $id,
            'user_id' => Auth::id(),
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $eventRequest = EventAssistanceRequest::where('event_assist_request_id', $id)->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($eventRequest->fk_user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request is Pending (should be after appeal)
        if (strtoupper($eventRequest->status) !== 'PENDING') {
            return response()->json(['error' => 'Only pending requests can be updated. Please appeal your rejected request first.'], 400);
        }

        // Use similar validation as store method
        try {
            $request->validate([
                'event_type' => ['nullable', 'string', 'max:255'],
                'purpose' => ['nullable', 'string'],
                'other_purpose' => ['nullable', 'string'],
                'event_location' => ['nullable', 'string'],
                'event_date' => ['nullable', 'date'],
                'event_start' => ['nullable', 'string'],
                'event_end' => ['nullable', 'string'],
                'expected_attendees' => ['nullable', 'integer', 'min:0'],
                'id_type' => ['nullable', 'string'],
                'valid_id_content' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                'id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                'documents' => ['nullable'],
                'documents.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
                'extra_fields' => ['nullable', 'array'],
                'extra_fields.*' => ['nullable'],
                'request_items' => ['nullable', 'array'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update basic fields
            $updateData = [];
            
            if ($request->filled('purpose')) {
                $updateData['purpose'] = $request->input('purpose');
            }
            if ($request->filled('other_purpose')) {
                $updateData['other_purpose'] = $request->input('other_purpose');
            }
            if ($request->filled('event_location')) {
                $updateData['event_location'] = $request->input('event_location');
            }
            if ($request->filled('event_date')) {
                $updateData['event_date'] = $request->input('event_date');
            }
            if ($request->filled('event_start')) {
                $updateData['event_start'] = $request->input('event_start');
            }
            if ($request->filled('event_end')) {
                $updateData['event_end'] = $request->input('event_end');
            }
            if ($request->filled('expected_attendees')) {
                $updateData['expected_attendees'] = $request->input('expected_attendees');
            }

            // Handle extra_fields - merge with existing, only update provided fields
            if ($request->has('extra_fields')) {
                $newExtraFields = $request->input('extra_fields', []);
                $existingExtraFields = $eventRequest->extra_fields ?? [];
                if (is_string($existingExtraFields)) {
                    try {
                        $decoded = json_decode($existingExtraFields, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $existingExtraFields = $decoded;
                        }
                    } catch (\Exception $e) {
                        $existingExtraFields = [];
                    }
                }
                
                // Merge: keep existing fields, update only the ones provided
                $mergedExtraFields = array_merge($existingExtraFields, $newExtraFields);
                $updateData['extra_fields'] = $mergedExtraFields;
            }

            // Handle valid_id_type update
            $fkValidIdType = $eventRequest->fk_valid_id_type;
            if ($request->filled('id_type')) {
                $idTypeString = $request->input('id_type');
                $validIdType = DB::table('valid_id_types')
                    ->where(function($query) use ($idTypeString) {
                        $query->where('id_type_name', $idTypeString)
                              ->orWhere('name', $idTypeString)
                              ->orWhere('type_name', $idTypeString)
                              ->orWhere('id_type', $idTypeString);
                    })
                    ->first();
                
                if ($validIdType) {
                    $fkValidIdType = $validIdType->valid_id_type_id ?? $validIdType->id ?? $fkValidIdType;
                }
            }
            $updateData['fk_valid_id_type'] = $fkValidIdType;

            // Handle file uploads - update valid_id_content if new file provided
            if ($request->hasFile('valid_id_content')) {
                $file = $request->file('valid_id_content');
                $updateData['valid_id_content'] = file_get_contents($file->getRealPath(), FILE_BINARY);
            } elseif ($request->hasFile('id_front')) {
                $file = $request->file('id_front');
                $updateData['valid_id_content'] = file_get_contents($file->getRealPath(), FILE_BINARY);
            }

            // Update created_at to current time so resubmission brings the request back to the top
            $updateData['created_at'] = now();

            // Update the event assistance request using raw SQL for valid_id_content
            if (isset($updateData['valid_id_content'])) {
                $pdo = DB::connection()->getPdo();
                $columns = array_keys($updateData);
                $placeholders = array_map(function($col) {
                    return "`$col` = ?";
                }, $columns);
                
                $values = array_values($updateData);
                $sql = "UPDATE event_assistance_requests SET " . implode(', ', $placeholders) . " WHERE event_assist_request_id = ?";
                $values[] = $id;
                
                $stmt = $pdo->prepare($sql);
                foreach ($values as $index => $value) {
                    $paramIndex = $index + 1;
                    $columnName = $index < count($columns) ? $columns[$index] : null;
                    
                    if ($columnName === 'valid_id_content') {
                        $stmt->bindValue($paramIndex, $value, \PDO::PARAM_LOB);
                    } elseif ($columnName === 'purpose') {
                        $purposeValue = $value ?? '';
                        $stmt->bindValue($paramIndex, $purposeValue, \PDO::PARAM_STR);
                    } elseif ($columnName === 'fk_valid_id_type') {
                        $stmt->bindValue($paramIndex, $value, \PDO::PARAM_INT);
                    } elseif ($columnName === 'extra_fields') {
                        $extraFieldsValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value;
                        $stmt->bindValue($paramIndex, $extraFieldsValue, \PDO::PARAM_STR);
                    } else {
                        $stmt->bindValue($paramIndex, $value);
                    }
                }
                
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $stmt->execute();
            } else {
                // No binary data to update, use regular Eloquent update
                if (isset($updateData['extra_fields']) && is_array($updateData['extra_fields'])) {
                    $updateData['extra_fields'] = json_encode($updateData['extra_fields'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                $eventRequest->update($updateData);
            }

            // Handle new file attachments
            if ($request->hasFile('id_front')) {
                $idFrontFile = $request->file('id_front');
                if ($idFrontFile && $idFrontFile->isValid()) {
                    $path = $idFrontFile->store('event_assistance_attachments', 'public');
                    
                    // Delete old attachment for this field if exists
                    EventAssistanceAttachment::where('fk_event_assist_request_id', $eventRequest->event_assist_request_id)
                        ->where('field_name', 'id_front')
                        ->delete();
                    
                    EventAssistanceAttachment::create([
                        'fk_event_assist_request_id' => $eventRequest->event_assist_request_id,
                        'field_name' => 'id_front',
                        'file_name' => $idFrontFile->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $idFrontFile->getMimeType(),
                        'file_size' => $idFrontFile->getSize(),
                    ]);
                }
            }

            if ($request->hasFile('id_back')) {
                $idBackFile = $request->file('id_back');
                if ($idBackFile && $idBackFile->isValid()) {
                    $path = $idBackFile->store('event_assistance_attachments', 'public');
                    
                    EventAssistanceAttachment::where('fk_event_assist_request_id', $eventRequest->event_assist_request_id)
                        ->where('field_name', 'id_back')
                        ->delete();
                    
                    EventAssistanceAttachment::create([
                        'fk_event_assist_request_id' => $eventRequest->event_assist_request_id,
                        'field_name' => 'id_back',
                        'file_name' => $idBackFile->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $idBackFile->getMimeType(),
                        'file_size' => $idBackFile->getSize(),
                    ]);
                }
            }

            // Handle extra_fields file attachments
            if ($request->has('extra_fields')) {
                $extraFields = $request->input('extra_fields', []);
                foreach ($extraFields as $fieldName => $value) {
                    if ($request->hasFile("extra_fields.{$fieldName}") || $request->hasFile("extra_fields[{$fieldName}]")) {
                        $file = $request->file("extra_fields.{$fieldName}") ?? $request->file("extra_fields[{$fieldName}]");
                        if ($file && $file->isValid()) {
                            $path = $file->store('event_assistance_attachments', 'public');
                            
                            // Delete old attachment for this field if exists
                            EventAssistanceAttachment::where('fk_event_assist_request_id', $eventRequest->event_assist_request_id)
                                ->where('field_name', $fieldName)
                                ->delete();
                            
                            // Create new attachment
                            EventAssistanceAttachment::create([
                                'fk_event_assist_request_id' => $eventRequest->event_assist_request_id,
                                'field_name' => $fieldName,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                        }
                    }
                }
            }

            // Handle request items update
            if ($request->has('request_items')) {
                $requestItems = $request->input('request_items', []);
                
                // Delete existing items
                EventAssistanceRequestItem::where('fk_event_assist_request_id', $eventRequest->event_assist_request_id)->delete();
                
                // Create new items
                foreach ($requestItems as $item) {
                    if (!empty($item['fk_event_assist_item_id'])) {
                        EventAssistanceRequestItem::create([
                            'fk_event_assist_request_id' => $eventRequest->event_assist_request_id,
                            'fk_event_assist_item_id' => $item['fk_event_assist_item_id'],
                            'quantity' => $item['quantity'] ?? 1,
                            'description' => $item['description'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            \Log::info('Event assistance request updated successfully', [
                'event_assist_request_id' => $id,
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully. Your request has been resubmitted for review.',
                'data' => [
                    'event_assist_request_id' => $eventRequest->event_assist_request_id,
                    'event_assist_request_ticket' => $eventRequest->event_assist_request_ticket,
                    'status' => $eventRequest->status,
                ],
            ]);
        } catch (\Throwable $ex) {
            DB::rollBack();
            \Log::error('Event assistance update exception: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update the request: ' . $ex->getMessage()], 500);
        }
    }

}
