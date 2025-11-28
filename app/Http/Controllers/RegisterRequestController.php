<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegistrationReq;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class RegisterRequestController extends Controller
{
    /**
     * Display a listing of registration requests for the Inertia page.
     */
    public function index()
    {
        // Fetch only requests with registration_status = 'pending'
        $requests = RegistrationReq::where('registration_status', 'pending')
            ->orderByDesc(RegistrationReq::CREATED_AT)
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

                $roleText = ((int) $r->fk_role_id === 1) ? 'Resident' : 'Official';

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

                $phase = $r->phase ?? null;
                $package = $r->package ?? null;
                if ((empty($phase) || empty($package)) && !empty($r->phase_package)) {
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

                return [
                    'id' => $r->registration_req_id,
                    'name' => $fullName,
                    'role' => $roleText,
                    'description' => $r->description ?? '',
                    'date' => $r->{RegistrationReq::CREATED_AT} 
                        ? Carbon::parse($r->{RegistrationReq::CREATED_AT})->format('m/d/Y') 
                        : null,
                    'date_raw' => $r->{RegistrationReq::CREATED_AT} 
                        ? Carbon::parse($r->{RegistrationReq::CREATED_AT})->toDateTimeString() 
                        : null,
                    'birthdate' => $r->birthdate ?? '',
                    'age' => $age !== null ? (string) $age : null,
                    'sex' => $r->sex ?? '',
                    'civilStatus' => $r->civil_status ?? $r->civilStatus ?? '',
                    'contact' => $r->contact_number ?? '',
                    'occupation' => $r->occupation ?? '',
                    'yearsInBarangay' => $r->years_in_barangay ?? '',
                    'phase' => $phase ?? '',
                    'package' => $package ?? '',
                    'address' => $r->address ?? '',
                    'registration_status' => $r->registration_status ?? null,
                    'offenses' => $r->offenses ?? null,
                    'admin_comment' => $r->admin_comment ?? null,
                ];
            });

        return Inertia::render('Admin/System_Admin/S_Register_Request', [
            'registerRequests' => $requests,
        ]);
    }


    /**
     * Approve a registration request: set registration_status => 'approved'
     * and create corresponding rows in `users` and `user_credentials`.
     */
    public function approve(Request $request, $id)
    {
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

                if (($r->registration_status ?? '') === 'approved') {
                    throw new \Exception('This request is already approved.');
                }

                // Build user data
                $userData = [
                    'last_name' => $r->last_name ?? null,
                    'first_name' => $r->first_name ?? null,
                    'middle_name' => $r->middle_name ?? null,
                    'suffix' => $r->suffix ?? null,
                    'sex' => $r->sex ?? null,
                    'birthdate' => $r->birthdate ?? null,
                    'civil_status' => $r->civil_status ?? null,
                    'address' => $r->address ?? null,
                    'profile_description' => $r->description ?? null,
                    'fk_role_id' => $r->fk_role_id ?? null,
                ];

                $user = \App\Models\User::create($userData);

                if (!$user || !$user->user_id) {
                    throw new \Exception('Failed to create user record.');
                }

                // Use password from registration_req as-is (already hashed)
                $hashedPassword = $r->password ?? null;

                $contactNumber = $request->input('contact_number') ?? $r->contact_number ?? null;
                $secondaryContact = $request->input('secondary_contact_number') ?? ($r->secondary_contact_number ?? null);

                // Create user credentials
                $credData = [
                    'fk_user_id' => $user->user_id,
                    'contact_number' => $contactNumber,
                    'secondary_contact_number' => $secondaryContact,
                    'password' => $hashedPassword,
                ];

                $userCred = \App\Models\UserCredential::create($credData);

                if (!$userCred || !$userCred->user_cred_id) {
                    throw new \Exception('Failed to create user credentials.');
                }

                // Update registration request to approved
                $r->registration_status = 'approved';

                if ($request->filled('comment') && Schema::hasColumn($r->getTable(), 'admin_comment')) {
                    $r->admin_comment = $request->input('comment');
                }

                if ($request->has('offenses') && Schema::hasColumn($r->getTable(), 'offenses')) {
                    $r->offenses = is_array($request->input('offenses')) ? json_encode($request->input('offenses')) : $request->input('offenses');
                }

                if (Schema::hasColumn($r->getTable(), 'user_id')) {
                    $r->user_id = $user->user_id;
                }

                if (Schema::hasColumn($r->getTable(), 'updated_at')) {
                    $r->updated_at = now();
                }

                $r->save();

                return [
                    'user_id' => $user->user_id,
                    'user_cred_id' => $userCred->user_cred_id,
                ];
            });

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
     * Reject a registration request: remove the row from registration_req table.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            // Keep these optional so the front-end can provide a reason/comment for logging elsewhere.
            'reason' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $r = RegistrationReq::findOrFail($id);

                // Optional: prevent deletion of already-approved users
                if (($r->registration_status ?? '') === 'approved') {
                    throw new \Exception('Cannot reject/delete an already approved request.');
                }

                // Permanently delete the registration request row
                $r->delete();
            });

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

}
