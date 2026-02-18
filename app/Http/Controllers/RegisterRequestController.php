<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegistrationReq;
use App\Models\User;
use App\Models\UserCredential;
use App\Services\OtpService;
use App\Services\SmsService;
use App\Services\BackgroundCheckService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class RegisterRequestController extends Controller
{
    /**
     * Display a listing of registration requests for the Inertia page.
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please sign in.',
                ], 401);
            }
            return redirect()->route('login_admin')->with('error', 'Please sign in to access this page.');
        }

        // If this is an API request (JSON), return JSON response for history
        if ($request->wantsJson() || $request->expectsJson()) {
            return $this->indexJson($request);
        }

        // Fetch only requests with registration_status = 'Pending'
        $requests = RegistrationReq::where('registration_status', 'Pending')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                // Build full name safely
                $parts = array_filter([
                    $r->first_name ?? null,
                    $r->middle_name ?? null,
                    $r->last_name ?? null,
                    $r->suffix ?? null,
                ]);
                $fullName = implode(' ', $parts) ?: 'Unknown';

                $roleText = ((int) ($r->fk_role_id ?? 0) === 1) ? 'Resident' : 'Official';

                // compute age from birthdate if available
                $age = null;
                if (!empty($r->birthdate)) {
                    try {
                        $age = Carbon::parse($r->birthdate)->age;
                    } catch (\Throwable $e) {
                        $age = null;
                    }
                } elseif (!empty($r->age)) {
                    $age = $r->age;
                }

                // Determine phase/package
                $phase = $r->phase ?? null;
                $package = $r->package ?? null;
                // legacy fallback if a combined field existed
                if ((empty($phase) || empty($package)) && !empty($r->phase_package ?? null)) {
                    if (preg_match('/(Phase\s*\d+)/i', $r->phase_package, $pmatch)) {
                        $phase = $pmatch[1];
                    }
                    if (preg_match('/(Package\s*\d+)/i', $r->phase_package, $kmatch)) {
                        $package = $kmatch[1];
                    }
                    if ((empty($phase) || empty($package)) && strpos($r->phase_package, ',') !== false) {
                        [$p1, $p2] = array_map('trim', explode(',', $r->phase_package, 2));
                        if (empty($phase)) $phase = $p1;
                        if (empty($package)) $package = $p2 ?? $package;
                    }
                }

                // Build a friendly address from available fields
                $addressParts = array_filter([
                    $r->house_number ?? null,
                    $r->street ?? null,
                    $r->phase ?? null,
                    $r->package ?? null,
                    $r->barangay ?? null,
                    $r->city ?? null,
                    $r->province ?? null,
                ]);
                $address = implode(', ', $addressParts);

                // Build proof URL if proof exists
                $proofUrl = null;
                $proofRaw = $r->proof ?? null;
                if ($proofRaw && $proofRaw !== 'none' && $proofRaw !== '') {
                    // If it's already a full URL, use it
                    if (str_starts_with($proofRaw, 'http://') || str_starts_with($proofRaw, 'https://')) {
                        $proofUrl = $proofRaw;
                    } elseif (str_starts_with($proofRaw, '/storage/') || str_starts_with($proofRaw, 'storage/')) {
                        $proofUrl = str_starts_with($proofRaw, '/') ? $proofRaw : '/' . $proofRaw;
                    } else {
                        // Assume it's a storage path
                        $proofUrl = '/storage/' . ltrim($proofRaw, '/');
                    }
                }

                return [
                    'id' => $r->registration_request_id,
                    'name' => $fullName,
                    'first_name' => $r->first_name ?? '',
                    'middle_name' => $r->middle_name ?? '',
                    'last_name' => $r->last_name ?? '',
                    'suffix' => $r->suffix ?? '',
                    'role' => $roleText,
                    'description' => $r->profile_description ?? $r->description ?? '',
                    'date' => $r->created_at ? Carbon::parse($r->created_at)->format('m/d/Y') : null,
                    'date_raw' => $r->created_at ? Carbon::parse($r->created_at)->toDateTimeString() : null,
                    'birthdate' => $r->birthdate ?? '',
                    'age' => $age !== null ? (string) $age : null,
                    'sex' => $r->sex ?? '',
                    'civilStatus' => $r->civil_status ?? '',
                    'contact' => $r->contact_number ?? '',
                    'secondary_contact' => $r->secondary_contact_number ?? '',
                    'email' => $r->email ?? '',
                    'occupation' => $r->occupation ?? '',
                    'yearsInBarangay' => $r->years_in_barangay ?? '',
                    'phase' => $phase ?? '',
                    'package' => $package ?? '',
                    'address' => $address,
                    'house_number' => $r->house_number ?? '',
                    'street' => $r->street ?? '',
                    'barangay' => $r->barangay ?? '',
                    'city' => $r->city ?? '',
                    'province' => $r->province ?? '',
                    'place_of_birth' => $r->place_of_birth ?? '',
                    'religion' => $r->religion ?? '',
                    'nationality' => $r->nationality ?? '',
                    'proof' => $proofUrl,
                    'proof_raw' => $proofRaw,
                    'proof_of_residency' => $r->proof_of_residency ?? '',
                    'registration_status' => $r->registration_status ?? null,
                    'offenses' => $r->offense_type ?? null,
                    'admin_comment' => $r->admin_feedback ?? null,
                ];
            });

        return Inertia::render('Admin/System_Admin/S_Register_Request', [
            'registerRequests' => $requests,
        ]);
    }


    /**
     * Approve a registration request: set registration_status => 'approved'
     * and move data from registration_requests into `users` and `user_credentials`.
     * Only columns that exist in the target models' fillable arrays will be added.
     */
    public function approve(Request $request, $id)
    {
        try {
            // Log immediately at the start - use error_log as backup
            error_log('=== REGISTRATION APPROVE METHOD CALLED === ID: ' . $id);
            \Log::info('=== REGISTRATION APPROVE METHOD CALLED ===', [
                'registration_request_id' => $id,
                'request_method' => $request->method(),
                'request_url' => $request->fullUrl(),
                'request_path' => $request->path(),
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $logEx) {
            error_log('Failed to log approval start: ' . $logEx->getMessage());
        }
        
        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::warning('Unauthorized approve attempt', [
                'registration_request_id' => $id,
                'ip' => $request->ip(),
            ]);
            
            if ($request->header('X-Inertia')) {
                return redirect()->route('login_admin')->with('error', 'Please sign in to continue.');
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please sign in.',
            ], 401);
        }

        $request->validate([
            'comment' => 'nullable|string',
            'offenses' => 'nullable|array',
            'offenses.*' => 'string',
            'contact_number' => 'nullable|string',
            'secondary_contact_number' => 'nullable|string',
        ]);

        try {
            $result = DB::transaction(function () use ($id, $request) {
                $r = RegistrationReq::findOrFail($id);

                if (strtolower($r->registration_status ?? '') === 'approved') {
                    throw new \Exception('This request is already approved.');
                }

                // Determine allowed fields from model fillables
                $userModel = new User();
                $userFillable = $userModel->getFillable();
                $userPrimary = $userModel->getKeyName();

                $credModel = new UserCredential();
                $credFillable = $credModel->getFillable();
                $credPrimary = $credModel->getKeyName();

                // Build user data only from registration request attributes that are allowed
                $userData = [];
                foreach ($userFillable as $col) {
                    // Skip primary key if present in fillable for safety (rare)
                    if ($col === $userPrimary) {
                        continue;
                    }

                    // Set created_at if it's in fillable
                    if ($col === 'created_at') {
                        $userData[$col] = $r->created_at ?? now();
                        continue;
                    }

                    // Handle place_of_birth - set to empty string if null (since column may not allow null)
                    if ($col === 'place_of_birth') {
                        $userData[$col] = $r->place_of_birth ?? '';
                        continue;
                    }

                    // Handle phase - normalize to "Phase X" format (VARCHAR, no ENUM restrictions)
                    if ($col === 'phase') {
                        $phaseValue = $r->phase ?? null;
                        if ($phaseValue) {
                            // Normalize to "Phase X" format
                            if (preg_match('/^Phase\s*(\d+)$/i', (string)$phaseValue, $matches)) {
                                $userData[$col] = 'Phase ' . $matches[1];
                            } elseif (preg_match('/^(\d+)$/', (string)$phaseValue, $matches)) {
                                // If it's just a number, convert to "Phase X" format
                                $userData[$col] = 'Phase ' . $matches[1];
                            } else {
                                // Already in correct format or invalid - use as is
                                $userData[$col] = trim((string)$phaseValue);
                            }
                        } else {
                            $userData[$col] = null;
                        }
                        \Log::info('Phase normalization during approval', [
                            'original' => $r->phase,
                            'normalized' => $userData[$col],
                            'registration_request_id' => $r->registration_request_id,
                        ]);
                        continue;
                    }

                    // Handle package - normalize to "Package X" format (VARCHAR, no ENUM restrictions)
                    if ($col === 'package') {
                        $packageValue = $r->package ?? null;
                        if ($packageValue) {
                            // Normalize to "Package X" format
                            if (preg_match('/^Package\s*(\d+)$/i', (string)$packageValue, $matches)) {
                                $userData[$col] = 'Package ' . $matches[1];
                            } elseif (preg_match('/^(\d+)$/', (string)$packageValue, $matches)) {
                                // If it's just a number, convert to "Package X" format
                                $userData[$col] = 'Package ' . $matches[1];
                            } else {
                                // Already in correct format or invalid - use as is
                                $userData[$col] = trim((string)$packageValue);
                            }
                        } else {
                            $userData[$col] = null;
                        }
                        \Log::info('Package normalization during approval', [
                            'original' => $r->package,
                            'normalized' => $userData[$col],
                            'registration_request_id' => $r->registration_request_id,
                        ]);
                        continue;
                    }

                    // direct copy if attribute exists on registration request
                    if (array_key_exists($col, $r->getAttributes())) {
                        $value = $r->getAttribute($col);
                        // Convert null to empty string for string columns that might not allow null
                        if ($value === null && in_array($col, ['place_of_birth', 'religion', 'nationality', 'occupation'])) {
                            $value = '';
                        }
                        $userData[$col] = $value;
                        continue;
                    }

                    // common alias/fallbacks
                    if ($col === 'profile_description') {
                        $userData[$col] = $r->profile_description ?? $r->description ?? $r->proof ?? null;
                    }
                }

                // Log phase and package values before creating user
                \Log::info('User data before creation', [
                    'registration_request_id' => $r->registration_request_id,
                    'phase' => $userData['phase'] ?? 'NOT SET',
                    'phase_type' => gettype($userData['phase'] ?? null),
                    'phase_value' => var_export($userData['phase'] ?? null, true),
                    'package' => $userData['package'] ?? 'NOT SET',
                    'package_type' => gettype($userData['package'] ?? null),
                    'package_value' => var_export($userData['package'] ?? null, true),
                    'user_data_keys' => array_keys($userData),
                ]);

                // Phase and package are already normalized to "Phase X" / "Package X" format above
                // No additional conversion needed since they're now VARCHAR

                // Create the user without ever specifying the primary key
                \Log::info('Creating user from registration request', [
                    'registration_request_id' => $r->registration_request_id,
                    'user_data_keys' => array_keys($userData),
                    'phase_final' => $userData['phase'] ?? 'NULL',
                    'package_final' => $userData['package'] ?? 'NULL',
                    'phase_type' => isset($userData['phase']) ? gettype($userData['phase']) : 'not set',
                    'package_type' => isset($userData['package']) ? gettype($userData['package']) : 'not set',
                    'name' => ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''),
                ]);
                
                // Use DB::table() directly to bypass Eloquent type conversion
                $userId = DB::table('users')->insertGetId($userData);
                
                if (!$userId) {
                    \Log::error('Failed to insert user record', [
                        'registration_request_id' => $r->registration_request_id,
                        'user_data' => $userData,
                    ]);
                    throw new \Exception('Failed to create user record.');
                }
                
                $user = User::find($userId);

                if (!$user || !$user->user_id) {
                    \Log::error('Failed to create user record', [
                        'registration_request_id' => $r->registration_request_id,
                        'user_data' => $userData,
                    ]);
                    throw new \Exception('Failed to create user record.');
                }
                
                \Log::info('User created successfully', [
                    'user_id' => $user->user_id,
                    'registration_request_id' => $r->registration_request_id,
                ]);

                // Build credentials data only from fields that the UserCredential model accepts
                $credData = [];
                foreach ($credFillable as $col) {
                    // Skip primary key if present in fillable for safety
                    if ($col === $credPrimary) {
                        continue;
                    }

                    // Ensure fk_user_id is set to the newly created user's id (do NOT try to set user_cred_id)
                    if ($col === 'fk_user_id') {
                        $credData[$col] = $user->user_id;
                        continue;
                    }

                    // Allow request override for contact numbers
                    // Normalize phone numbers (remove non-digits) to match login behavior
                    if ($col === 'contact_number') {
                        $rawNumber = $request->input('contact_number') ?? $r->contact_number ?? null;
                        $credData[$col] = $rawNumber ? preg_replace('/\D/', '', $rawNumber) : null;
                        continue;
                    }

                    if ($col === 'secondary_contact_number') {
                        $rawNumber = $request->input('secondary_contact_number') ?? $r->secondary_contact_number ?? null;
                        $credData[$col] = $rawNumber ? preg_replace('/\D/', '', $rawNumber) : null;
                        continue;
                    }

                    // Copy email/password if present on registration request
                    if (array_key_exists($col, $r->getAttributes())) {
                        $credData[$col] = $r->getAttribute($col);
                        continue;
                    }

                    if ($col === 'email') {
                        $credData[$col] = $r->email ?? null;
                    } elseif ($col === 'password') {
                        // We copy password as-is (assumed hashed from registration)
                        $password = $r->password ?? null;
                        if (empty($password)) {
                            throw new \Exception('Password is missing from registration request. Cannot create user credentials.');
                        }
                        $credData[$col] = $password;
                    }
                }

                // As a guard, ensure fk_user_id exists
                if (!array_key_exists('fk_user_id', $credData)) {
                    $credData['fk_user_id'] = $user->user_id;
                }

                // Create credential (do NOT pass user_cred_id)
                \Log::info('Creating user credentials from registration request', [
                    'user_id' => $user->user_id,
                    'registration_request_id' => $r->registration_request_id,
                    'has_password' => !empty($credData['password']),
                    'has_contact_number' => !empty($credData['contact_number']),
                ]);
                
                $userCred = UserCredential::create($credData);

                if (!$userCred || !$userCred->user_cred_id) {
                    \Log::error('Failed to create user credentials', [
                        'user_id' => $user->user_id,
                        'registration_request_id' => $r->registration_request_id,
                        'cred_data_keys' => array_keys($credData),
                    ]);
                    throw new \Exception('Failed to create user credentials.');
                }
                
                \Log::info('User credentials created successfully', [
                    'user_cred_id' => $userCred->user_cred_id,
                    'user_id' => $user->user_id,
                    'registration_request_id' => $r->registration_request_id,
                ]);

                // Perform automatic background check
                try {
                    $backgroundCheckService = new BackgroundCheckService();
                    
                    // Match registrant against resident database
                    $birthdate = $r->birthdate ? Carbon::parse($r->birthdate)->format('Y-m-d') : null;
                    $matchedResident = null;
                    
                    if ($birthdate) {
                        $matchedResident = $backgroundCheckService->findMatchingResident(
                            $r->first_name ?? '',
                            $r->middle_name ?? null,
                            $r->last_name ?? '',
                            $r->suffix ?? null,
                            $birthdate
                        );
                    }
                    
                    if ($matchedResident) {
                        \Log::info('Resident match found, performing background check', [
                            'user_id' => $user->user_id,
                            'resident_id' => $matchedResident->resident_id,
                        ]);
                        
                        // Calculate restrictions based on offenses
                        $restrictionData = $backgroundCheckService->calculateRestrictions($matchedResident);
                        
                        // Copy offenses to user_offenses table
                        $backgroundCheckService->copyOffensesToUser($user->user_id, $matchedResident);
                        
                        // Apply restrictions if any
                        if ($restrictionData['has_restrictions']) {
                            $backgroundCheckService->applyRestrictions(
                                $user->user_id,
                                $restrictionData,
                                Auth::id() // Admin who approved
                            );
                            
                            \Log::info('Restrictions applied automatically', [
                                'user_id' => $user->user_id,
                                'restricted_document_types' => $restrictionData['restricted_document_types'],
                                'restricted_event_types' => $restrictionData['restricted_event_types'],
                            ]);
                        } else {
                            \Log::info('No restrictions needed - resident has no Medium/High offenses', [
                                'user_id' => $user->user_id,
                                'resident_id' => $matchedResident->resident_id,
                            ]);
                        }
                    } else {
                        \Log::info('No resident match found - no background check performed', [
                            'user_id' => $user->user_id,
                            'name' => ($r->first_name ?? '') . ' ' . ($r->last_name ?? ''),
                            'birthdate' => $birthdate,
                        ]);
                    }
                } catch (\Throwable $bgCheckEx) {
                    // Don't fail the approval if background check fails
                    \Log::error('Background check error during approval', [
                        'error' => $bgCheckEx->getMessage(),
                        'trace' => $bgCheckEx->getTraceAsString(),
                        'user_id' => $user->user_id ?? null,
                        'registration_request_id' => $r->registration_request_id,
                    ]);
                }

                // Update registration status INSIDE transaction to ensure atomicity
                // Use DB::table() directly since model has timestamps disabled
                $rowsAffected = DB::table('registration_requests')
                    ->where('registration_request_id', $id)
                    ->update(['registration_status' => 'Approved']);
                
                if ($rowsAffected === 0) {
                    \Log::error('Failed to update registration status - no rows affected', [
                        'registration_request_id' => $id,
                    ]);
                    throw new \Exception('Failed to update registration status.');
                }
                
                // Verify the update by querying directly
                $updatedStatus = DB::table('registration_requests')
                    ->where('registration_request_id', $id)
                    ->value('registration_status');
                
                \Log::info('Registration request status updated inside transaction', [
                    'registration_request_id' => $id,
                    'status' => 'Approved',
                    'rows_affected' => $rowsAffected,
                    'verified_status' => $updatedStatus,
                ]);
                
                if ($updatedStatus !== 'Approved') {
                    \Log::error('Status update verification failed', [
                        'registration_request_id' => $id,
                        'expected' => 'Approved',
                        'actual' => $updatedStatus,
                    ]);
                    throw new \Exception('Status update verification failed.');
                }

                // Store phone number and name for SMS (before transaction completes)
                $phoneNumber = $r->contact_number ?? $r->secondary_contact_number ?? null;
                $fullName = trim(implode(' ', array_filter([
                    $r->first_name ?? '',
                    $r->middle_name ?? '',
                    $r->last_name ?? '',
                    $r->suffix ?? '',
                ]))) ?: 'User';

                return [
                    'user_id' => $user->user_id,
                    'user_cred_id' => $userCred->user_cred_id,
                    'phone_number' => $phoneNumber,
                    'full_name' => $fullName,
                ];
            });

            \Log::info('Transaction completed, preparing SMS notification', [
                'registration_request_id' => $id,
                'result_keys' => array_keys($result ?? []),
                'result' => $result,
            ]);

            // Send SMS notification for approval (outside transaction)
            // Get phone number from the registration request directly since we have the ID
            try {
                // Reload the registration request to get phone number
                $r = RegistrationReq::find($id);
                $phoneNumber = $r ? ($r->contact_number ?? $r->secondary_contact_number ?? null) : null;
                $fullName = $r ? trim(implode(' ', array_filter([
                    $r->first_name ?? '',
                    $r->middle_name ?? '',
                    $r->last_name ?? '',
                    $r->suffix ?? '',
                ]))) : 'User';
                
                \Log::info('Registration approval SMS attempt - START', [
                    'registration_request_id' => $id,
                    'has_phone' => !empty($phoneNumber),
                    'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
                    'full_name' => $fullName,
                ]);
                
                if ($phoneNumber) {
                    $message = "Hello {$fullName}, your registration request has been APPROVED. You can now log in to iKonek176B using your contact number and password. Thank you!";
                    
                    \Log::info('Calling SMS service for registration approval', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message, // Log full message for debugging
                    ]);
                    
                    $smsService = new SmsService();
                    \Log::info('About to call sendSms method via Semaphore', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'provider' => $smsService->getProviderName(),
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned', [
                        'result' => $smsResult,
                    ]);
                    
                    \Log::info('SMS service result for registration approval', [
                        'success' => $smsResult['success'] ?? false,
                        'message' => $smsResult['message'] ?? 'No message',
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send approval SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                        ]);
                    }
                } else {
                    \Log::warning('No phone number available for registration approval SMS', [
                        'registration_request_id' => $id,
                    ]);
                }
            } catch (\Throwable $smsEx) {
                // Don't fail the approval if SMS fails
                \Log::error('SMS notification error during approval', [
                    'error' => $smsEx->getMessage(),
                    'trace' => $smsEx->getTraceAsString(),
                    'registration_request_id' => $id,
                ]);
            }

            if ($request->header('X-Inertia')) {
                return redirect()->back()->with([
                    'success' => 'Request approved and user created.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Request approved and user created.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            // Log the error to help debugging
            \Log::error('Approve error: '.$e->getMessage(), ['id' => $id, 'exception' => $e]);

            if ($request->header('X-Inertia')) {
                return redirect()->back()->with([
                    'success' => false,
                    'error' => 'Could not approve request: ' . $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Could not approve request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Reject a registration request: remove the row from registration_requests table.
     */
    public function reject(Request $request, $id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::warning('Unauthorized reject attempt', [
                'registration_request_id' => $id,
                'ip' => $request->ip(),
            ]);
            
            if ($request->header('X-Inertia')) {
                return redirect()->route('login_admin')->with('error', 'Please sign in to continue.');
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please sign in.',
            ], 401);
        }

        \Log::info('=== REGISTRATION REJECT METHOD CALLED ===', [
            'registration_request_id' => $id,
            'user_id' => Auth::id(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $request->validate([
            // Keep these optional so the front-end can provide a reason/comment for logging elsewhere.
            'reason' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        // Get phone number and name BEFORE deletion
        $r = RegistrationReq::findOrFail($id);
        $phoneNumber = $r->contact_number ?? $r->secondary_contact_number ?? null;
        $fullName = trim(implode(' ', array_filter([
            $r->first_name ?? '',
            $r->middle_name ?? '',
            $r->last_name ?? '',
            $r->suffix ?? '',
        ]))) ?: 'User';
        $rejectionReason = $request->input('reason') ?? $request->input('comment') ?? '';

        try {
            DB::transaction(function () use ($id, $request) {
                $r = RegistrationReq::findOrFail($id);

                // Optional: prevent deletion of already-approved users
                if (strtolower($r->registration_status ?? '') === 'approved') {
                    throw new \Exception('Cannot reject/delete an already approved request.');
                }

                // Permanently delete the registration request row
                $r->delete();
            });

            \Log::info('Transaction completed, preparing SMS notification for rejection', [
                'registration_request_id' => $id,
            ]);

            // Send SMS notification for rejection (outside transaction)
            try {
                \Log::info('Registration rejection SMS attempt - START', [
                    'registration_request_id' => $id,
                    'has_phone' => !empty($phoneNumber),
                    'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
                ]);

                if ($phoneNumber) {
                    $message = "Hello {$fullName}, your registration request has been REJECTED.";
                    if (!empty($rejectionReason)) {
                        $message .= " Reason: " . substr($rejectionReason, 0, 100);
                    }
                    $message .= " Please contact the barangay office for more information. Thank you.";
                    
                    \Log::info('Calling SMS service for registration rejection', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message, // Log full message for debugging
                    ]);
                    
                    $smsService = new SmsService();
                    \Log::info('About to call sendSms method for rejection via Semaphore', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'provider' => $smsService->getProviderName(),
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned for rejection', [
                        'result' => $smsResult,
                    ]);
                    
                    \Log::info('SMS service result for registration rejection', [
                        'success' => $smsResult['success'] ?? false,
                        'message' => $smsResult['message'] ?? 'No message',
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send rejection SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                        ]);
                    }
                } else {
                    \Log::warning('No phone number available for registration rejection SMS', [
                        'registration_request_id' => $id,
                    ]);
                }
            } catch (\Throwable $smsEx) {
                // Don't fail the rejection if SMS fails
                \Log::error('SMS notification error during rejection', [
                    'error' => $smsEx->getMessage(),
                    'trace' => $smsEx->getTraceAsString(),
                    'registration_request_id' => $id,
                ]);
            }

            // Inertia callers must receive an Inertia response (redirect back with flash)
            if ($request->header('X-Inertia')) {
                return redirect()->back()->with('success', 'Request rejected and removed.');
            }

            // Non-Inertia (API/AJAX) callers get JSON
            return response()->json([
                'success' => true,
                'message' => 'Request rejected and removed.',
            ]);
        } catch (\Throwable $e) {
            // Inertia response on error
            if ($request->header('X-Inertia')) {
                return redirect()->back()->with('error', 'Could not reject/delete request: ' . $e->getMessage());
            }

            // JSON error for non-Inertia
            return response()->json([
                'success' => false,
                'message' => 'Could not reject request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * JSON API endpoint for history/filtering - returns approved registration requests
     */
    private function indexJson(Request $request)
    {
        $authUser = $request->user();
        $allowedRoles = [9]; // system admin role id
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;

        if (! in_array($userRole, $allowedRoles, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requestedStatus = $request->query('status', 'Approved');
        $query = RegistrationReq::with('user')->orderBy('created_at', 'desc');

        if (is_string($requestedStatus) && strtolower($requestedStatus) !== 'all') {
            $statusFilter = ucfirst(strtolower($requestedStatus));
            $query->where('registration_status', $statusFilter);
        }

        $rows = $query->get();

        $registrationRequests = $rows->map(function ($r) {
            // Build full name safely
            $parts = array_filter([
                $r->first_name ?? null,
                $r->middle_name ?? null,
                $r->last_name ?? null,
                $r->suffix ?? null,
            ]);
            $fullName = implode(' ', $parts) ?: 'Unknown';

            $roleId = $r->fk_role_id ?? 1;
            $roleMap = [
                1 => 'Resident',
                2 => 'Barangay Captain',
                3 => 'Barangay Secretary',
                4 => 'Barangay Treasurer',
                5 => 'Barangay Kagawad',
                6 => 'SK Chairman',
                7 => 'Sangguniang Kabataan Kagawad',
                9 => 'System Admin',
            ];
            $role = $roleMap[$roleId] ?? 'Resident';

            // Get profile picture from the created user if available
            $profileImg = '/assets/DEFAULT.jpg';
            if ($r->user && $r->user->profile_pic) {
                $userProfilePic = $r->user->profile_pic;
                if (str_starts_with($userProfilePic, 'http')) {
                    $profileImg = $userProfilePic;
                } else {
                    $profileImg = str_starts_with($userProfilePic, '/storage/') 
                        ? $userProfilePic 
                        : '/storage/' . ltrim($userProfilePic, '/');
                }
            }

            return [
                'registration_request_id' => $r->registration_request_id,
                'name' => $fullName,
                'first_name' => $r->first_name ?? '',
                'last_name' => $r->last_name ?? '',
                'middle_name' => $r->middle_name ?? '',
                'suffix' => $r->suffix ?? '',
                'role' => $role,
                'role_id' => $roleId,
                'registration_status' => $r->registration_status ?? 'Pending',
                'created_at' => $r->created_at?->toDateTimeString(),
                'updated_at' => $r->updated_at?->toDateTimeString() ?? $r->created_at?->toDateTimeString(),
                'profile_pic' => $profileImg,
            ];
        });

        return response()->json([
            'registration_requests' => $registrationRequests,
        ]);
    }
}
