<?php

namespace App\Http\Controllers;

use App\Models\RegistrationReq;
use App\Models\User;
use App\Models\Post;
use App\Models\DocumentRequest;
use App\Models\EventAssistanceRequest;
use App\Models\UserRestriction;
use App\Models\UserCredential;
use App\Models\AdminCredential;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the system admin dashboard with statistics and user management.
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'registration_requests' => RegistrationReq::where('registration_status', 'Pending')->count(),
            'services_approved' => DocumentRequest::where('status', 'Approved')->count() 
                + EventAssistanceRequest::where('status', 'Approved')->count(),
            'total_posts' => Post::count(),
            'total_residents' => User::where('fk_role_id', 1)->count(),
        ];

        // Get all users with their post and comment counts
        $users = User::with('credential')
            ->get()
            ->map(function ($user) {
                // Count posts
                $postCount = Post::where('fk_post_author_id', $user->user_id)->count();
                
                // Count comments - use fk_commenter_id based on PostCommentController
                $commentCount = DB::table('post_comments')
                    ->where('fk_commenter_id', $user->user_id)
                    ->count();

                // Build full address
                $addressParts = array_filter([
                    $user->house_number ?? null,
                    $user->street ?? null,
                    $user->phase ?? null,
                    $user->package ?? null,
                    $user->barangay ?? null,
                    $user->city ?? null,
                    $user->province ?? null,
                ]);
                $address = implode(' ', $addressParts) ?: 'N/A';
                
                // Also create a sortable date for frontend
                $joinedDate = $user->created_at 
                    ? Carbon::parse($user->created_at)
                    : null;

                // Get contact number from credential if available
                $contact = $user->credential?->contact_number ?? 'N/A';

                // Determine role name
                $roleId = $user->fk_role_id ?? 1;
                $roleName = $roleId === 1 ? 'Resident' : 'Official';

                // Format date joined
                $joined = $user->created_at 
                    ? Carbon::parse($user->created_at)->format('m/d/Y')
                    : 'N/A';

                // Get profile picture
                $profilePic = $user->profile_pic 
                    ? (str_starts_with($user->profile_pic, 'http') || str_starts_with($user->profile_pic, '/storage/') 
                        ? $user->profile_pic 
                        : '/storage/' . $user->profile_pic)
                    : '/assets/ADMIN.png';

                // Get user restrictions
                $userRestriction = UserRestriction::where('user_id', $user->user_id)->first();
                $isRestricted = $userRestriction && (
                    $userRestriction->restrict_posting ||
                    $userRestriction->restrict_commenting ||
                    $userRestriction->restrict_document_request ||
                    $userRestriction->restrict_event_assistance_request ||
                    !empty($userRestriction->restricted_document_types) ||
                    !empty($userRestriction->restricted_event_types)
                );

                return [
                    'id' => $user->user_id,
                    'profile' => $profilePic,
                    'name' => $user->name,
                    'address' => $address,
                    'contact' => $contact,
                    'role' => $roleName,
                    'role_id' => $roleId,
                    'posts' => $postCount,
                    'comments' => $commentCount,
                    'joined' => $joined,
                    'joined_timestamp' => $joinedDate ? $joinedDate->timestamp : 0,
                    'is_restricted' => $isRestricted,
                    'restrictions' => $userRestriction ? [
                        'posting' => (bool) $userRestriction->restrict_posting,
                        'commenting' => (bool) $userRestriction->restrict_commenting,
                        'documentRequest' => (bool) $userRestriction->restrict_document_request,
                        'eventAssistanceRequest' => (bool) $userRestriction->restrict_event_assistance_request,
                        'restrictedDocumentTypes' => $userRestriction->restricted_document_types ?? [],
                        'allowedDocumentTypes' => $userRestriction->allowed_document_types ?? [],
                        'restrictedEventTypes' => $userRestriction->restricted_event_types ?? [],
                        'allowedEventTypes' => $userRestriction->allowed_event_types ?? [],
                        'restrictionReason' => $userRestriction->restriction_reason ?? null,
                    ] : null,
                ];
            })
            ->sortByDesc('joined_timestamp')
            ->values();

        // Get all document types for granular restrictions
        $documentTypes = DocumentType::all()->map(function ($docType) {
            return [
                'id' => $docType->document_type_id,
                'name' => $docType->document_name,
            ];
        })->values();

        // Define all event assistance types
        $eventTypes = [
            ['name' => 'Court Reservation'],
            ['name' => 'Community Hall Reservation'],
            ['name' => 'Manpower Assistance'],
            ['name' => 'Tent and Tables Borrowing'],
            ['name' => 'Sports Equipment Borrowing'],
            ['name' => 'Sound System Borrowing'],
        ];

        return Inertia::render('Admin/System_Admin/S_Dashboard', [
            'stats' => $stats,
            'users' => $users,
            'documentTypes' => $documentTypes,
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Restrict a user
     */
    public function restrictUser(Request $request, $userId)
    {
        $request->validate([
            'restrictions' => 'required|array',
            'restrictions.posting' => 'boolean',
            'restrictions.commenting' => 'boolean',
            'restrictions.documentRequest' => 'boolean',
            'restrictions.eventAssistanceRequest' => 'boolean',
            'restrictions.restrictedDocumentTypes' => 'nullable|array',
            'restrictions.restrictedDocumentTypes.*' => 'integer|exists:document_types,document_type_id',
            'restrictions.allowedDocumentTypes' => 'nullable|array',
            'restrictions.allowedDocumentTypes.*' => 'integer|exists:document_types,document_type_id',
            'restrictions.restrictedEventTypes' => 'nullable|array',
            'restrictions.restrictedEventTypes.*' => 'string',
            'restrictions.allowedEventTypes' => 'nullable|array',
            'restrictions.allowedEventTypes.*' => 'string',
            'restrictionReason' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $restrictions = $request->input('restrictions');
            $adminId = Auth::id();

            // Get existing restrictions to compare
            $existingRestriction = UserRestriction::where('user_id', $userId)->first();
            $existingRestrictions = $existingRestriction ? [
                'posting' => (bool) $existingRestriction->restrict_posting,
                'commenting' => (bool) $existingRestriction->restrict_commenting,
                'documentRequest' => (bool) $existingRestriction->restrict_document_request,
                'eventAssistanceRequest' => (bool) $existingRestriction->restrict_event_assistance_request,
            ] : [
                'posting' => false,
                'commenting' => false,
                'documentRequest' => false,
                'eventAssistanceRequest' => false,
            ];

            // Build restriction reason
            $restrictionReason = $request->input('restrictionReason');
            if (empty($restrictionReason) && $existingRestriction && $existingRestriction->restriction_reason) {
                // Keep existing reason if no new one provided
                $restrictionReason = $existingRestriction->restriction_reason;
            } elseif (empty($restrictionReason)) {
                // Default reason if none provided
                $restrictionReason = 'Restriction applied by system administrator.';
            }

            // Create or update user restrictions
            $userRestriction = UserRestriction::updateOrCreate(
                ['user_id' => $userId],
                [
                    'restrict_posting' => $restrictions['posting'] ?? false,
                    'restrict_commenting' => $restrictions['commenting'] ?? false,
                    'restrict_document_request' => $restrictions['documentRequest'] ?? false,
                    'restrict_event_assistance_request' => $restrictions['eventAssistanceRequest'] ?? false,
                    'restricted_document_types' => $restrictions['restrictedDocumentTypes'] ?? [],
                    'allowed_document_types' => $restrictions['allowedDocumentTypes'] ?? [],
                    'restricted_event_types' => $restrictions['restrictedEventTypes'] ?? [],
                    'allowed_event_types' => $restrictions['allowedEventTypes'] ?? [],
                    'restriction_reason' => $restrictionReason,
                    'restricted_by' => $adminId,
                ]
            );

            // Determine what changed
            $addedRestrictions = [];
            $removedRestrictions = [];

            if (($restrictions['posting'] ?? false) && !$existingRestrictions['posting']) {
                $addedRestrictions[] = 'Posting: This restriction may be due to posting inappropriate content, spam, or violating community guidelines.';
            } elseif (!($restrictions['posting'] ?? false) && $existingRestrictions['posting']) {
                $removedRestrictions[] = 'Posting';
            }

            if (($restrictions['commenting'] ?? false) && !$existingRestrictions['commenting']) {
                $addedRestrictions[] = 'Commenting: This restriction may be due to inappropriate comments, harassment, or disruptive behavior in discussions.';
            } elseif (!($restrictions['commenting'] ?? false) && $existingRestrictions['commenting']) {
                $removedRestrictions[] = 'Commenting';
            }

            if (($restrictions['documentRequest'] ?? false) && !$existingRestrictions['documentRequest']) {
                $addedRestrictions[] = 'Document Request: This restriction may be due to misuse of document request services, providing false information, or violating request policies.';
            } elseif (!($restrictions['documentRequest'] ?? false) && $existingRestrictions['documentRequest']) {
                $removedRestrictions[] = 'Document Request';
            }

            if (($restrictions['eventAssistanceRequest'] ?? false) && !$existingRestrictions['eventAssistanceRequest']) {
                $addedRestrictions[] = 'Event Assistance Request: This restriction may be due to misuse of event assistance services, providing false information, or violating assistance policies.';
            } elseif (!($restrictions['eventAssistanceRequest'] ?? false) && $existingRestrictions['eventAssistanceRequest']) {
                $removedRestrictions[] = 'Event Assistance Request';
            }

            // Create notification for added restrictions
            if (!empty($addedRestrictions)) {
                $notificationMessage = 'Your account has been restricted. Restrictions applied: ' . implode(' ', $addedRestrictions);
                
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'Report',
                    'notification_reference_id' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }

            // Create notification for removed restrictions
            if (!empty($removedRestrictions)) {
                $removedList = implode(', ', $removedRestrictions);
                $notificationMessage = 'Some of your account restrictions have been removed. You can now access: ' . $removedList . '.';
                
                // If all restrictions were removed
                if (empty($restrictions['posting'] ?? false) && 
                    empty($restrictions['commenting'] ?? false) && 
                    empty($restrictions['documentRequest'] ?? false) && 
                    empty($restrictions['eventAssistanceRequest'] ?? false)) {
                    $notificationMessage = 'All of your account restrictions have been removed. You can now access all features again.';
                }
                
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'Report',
                    'notification_reference_id' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }

            // If all restrictions are false and there was a restriction record, delete it
            // Also check granular restrictions
            $hasGranularRestrictions = !empty($restrictions['restrictedDocumentTypes'] ?? []) ||
                                      !empty($restrictions['restrictedEventTypes'] ?? []);
            
            if (empty($restrictions['posting'] ?? false) && 
                empty($restrictions['commenting'] ?? false) && 
                empty($restrictions['documentRequest'] ?? false) && 
                empty($restrictions['eventAssistanceRequest'] ?? false) &&
                !$hasGranularRestrictions &&
                $userRestriction) {
                $userRestriction->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User restrictions applied successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply restrictions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unrestrict a user (remove all restrictions)
     */
    public function unrestrictUser($userId)
    {
        try {
            DB::beginTransaction();

            // Find and delete user restrictions
            $userRestriction = UserRestriction::where('user_id', $userId)->first();
            
            if ($userRestriction) {
                // Create notification for unrestriction
                $notificationMessage = 'Your account restrictions have been removed. You can now access all features again.';
                
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'Report',
                    'notification_reference_id' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                ]);

                // Delete the restriction record
                $userRestriction->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User restrictions removed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove restrictions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $userId)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($userId);
        $credential = $user->credential;

        if (!$credential) {
            return response()->json([
                'success' => false,
                'message' => 'User credential not found'
            ], 404);
        }

        $credential->password = bcrypt($request->password);
        $credential->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Delete a user and all related data
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deleting system admin
        if ($user->fk_role_id == 9) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system administrator'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Get phone numbers before deleting credentials (for OTP cache cleanup)
            $phoneNumbers = [];
            
            // Delete user credentials
            $userCredential = UserCredential::where('fk_user_id', $userId)->first();
            if ($userCredential) {
                if ($userCredential->contact_number) {
                    $phoneNumbers[] = $userCredential->contact_number;
                }
                if ($userCredential->secondary_contact_number) {
                    $phoneNumbers[] = $userCredential->secondary_contact_number;
                }
                $userCredential->delete();
            }

            // Delete admin credentials if exists
            $adminCredential = AdminCredential::where('fk_user_id', $userId)->first();
            if ($adminCredential) {
                $adminCredential->delete();
            }

            // Delete registration requests associated with this user's phone numbers
            if (!empty($phoneNumbers)) {
                RegistrationReq::where(function($query) use ($phoneNumbers) {
                    $query->whereIn('contact_number', $phoneNumbers)
                          ->orWhereIn('secondary_contact_number', $phoneNumbers);
                })->delete();
            }
            
            // Also try to find registration requests by matching user details
            // (in case phone numbers don't match exactly)
            RegistrationReq::where('first_name', $user->first_name)
                ->where('last_name', $user->last_name)
                ->where(function($query) use ($user) {
                    if ($user->middle_name) {
                        $query->where('middle_name', $user->middle_name);
                    }
                })
                ->delete();

            // Clear OTP cache entries for this user's phone numbers
            foreach ($phoneNumbers as $phone) {
                $normalizedPhone = preg_replace('/\D+/', '', $phone);
                if (strlen($normalizedPhone) === 10 && $normalizedPhone[0] === '9') {
                    $normalizedPhone = '0' . $normalizedPhone;
                }
                Cache::forget("otp:{$normalizedPhone}");
                Cache::forget("otp_sent:{$normalizedPhone}");
                Cache::forget("phone_verified:{$normalizedPhone}");
                Cache::forget("password_reset_otp_sent:{$normalizedPhone}");
                Cache::forget("password_reset_verified:{$normalizedPhone}");
            }

            // Delete user restrictions
            UserRestriction::where('fk_user_id', $userId)->delete();

            // Delete the user (this should cascade delete other related records if foreign keys are set up)
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User and all related data deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}

















