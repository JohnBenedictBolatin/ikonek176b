<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterOfficialController extends Controller
{
    public function store(Request $request)
    {
        // Log incoming request for debugging (excluding password)
        Log::info('RegisterOfficialController::store - Incoming request', [
            'has_password' => $request->has('password'),
            'password_length' => $request->has('password') ? strlen($request->input('password')) : 0,
            'has_contact_number' => $request->has('contact_number'),
            'contact_number' => $request->input('contact_number'),
            'has_fk_role_id' => $request->has('fk_role_id'),
            'fk_role_id' => $request->input('fk_role_id'),
            'all_keys' => array_keys($request->except(['password'])),
        ]);

        // Validation: adjust rules as needed.
        $data = $request->validate([
            'last_name' => ['required','string','max:255'],
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'suffix' => ['nullable','string','max:50'],

            // contact numbers: accept 10-11 digits, will normalize to 10 digits. Unique on user_credentials.contact_number
            'contact_number' => ['required','regex:/^[0-9]{10,11}$/', Rule::unique('user_credentials','contact_number')],
            'secondary_contact_number' => ['nullable','regex:/^[0-9]{10,11}$/'],

            // address fields mapped to users table
            'house' => ['nullable','string','max:255'],
            'street' => ['nullable','string','max:255'],
            'house_number' => ['nullable','string','max:255'],
            'barangay_input' => ['nullable','string','max:255'],
            'barangay' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'province' => ['nullable','string','max:255'],

            'phase' => ['nullable','string'],
            'package' => ['nullable','string'],

            'password' => ['required','string','min:8'],
            'sex' => ['nullable','string','max:50'],
            'birthdate' => ['nullable','date'],
            'civil_status' => ['nullable','string','max:50'],
            'fk_role_id' => ['required','integer'],

            // additional personal fields
            'religion' => ['nullable','string','max:255'],
            'nationality' => ['nullable','string','max:255'],
            'occupation' => ['nullable','string','max:255'],
            'place_of_birth' => ['nullable','string','max:255'],

            // email belongs to user_credentials table in your schema
            'email' => ['nullable','email', Rule::unique('user_credentials','email')],

            // legacy combined address (optional)
            'address' => ['nullable','string'],
        ]);

        // DEFAULT SUFFIX: make empty string if not provided
        if (empty($data['suffix'])) {
            $data['suffix'] = '';
        }

        // Normalize phone numbers: store as digits only (remove non-digits and leading 0)
        if (!empty($data['contact_number'])) {
            $contact = preg_replace('/\D+/', '', $data['contact_number']);
            if (Str::startsWith($contact, '0')) {
                $contact = ltrim($contact, '0'); // remove leading 0 if present
            }
            // Ensure it's exactly 10 digits after normalization
            if (strlen($contact) !== 10) {
                return back()->withInput()
                             ->withErrors(['contact_number' => 'Contact number must be exactly 10 digits (without country code).']);
            }
            $data['contact_number'] = $contact;
        }

        if (!empty($data['secondary_contact_number'])) {
            $secondary = preg_replace('/\D+/', '', $data['secondary_contact_number']);
            if (Str::startsWith($secondary, '0')) {
                $secondary = ltrim($secondary, '0');
            }
            // Ensure it's exactly 10 digits after normalization (if provided)
            if (!empty($secondary) && strlen($secondary) !== 10) {
                return back()->withInput()
                             ->withErrors(['secondary_contact_number' => 'Secondary contact number must be exactly 10 digits (without country code).']);
            }
            $data['secondary_contact_number'] = $secondary ?: null;
        }

        // Trim whitespace for optional fields
        if (!empty($data['phase'])) {
            $data['phase'] = trim($data['phase']);
        }
        if (!empty($data['package'])) {
            $data['package'] = trim($data['package']);
        }

        // Hash password (we will store hashed password in user_credentials.password)
        $hashedPassword = Hash::make($data['password']);

        // Map role_id to occupation (since employees' occupation is their role)
        $roleToOccupation = [
            2 => 'Barangay Captain',
            3 => 'Barangay Secretary',
            4 => 'Barangay Treasurer',
            5 => 'Barangay Kagawad',
            6 => 'SK Chairman',
            7 => 'SK Kagawad',
        ];

        // Set occupation to role name if not provided (since employees already have a position/role)
        $occupation = $data['occupation'] ?? null;
        if (empty($occupation) && isset($roleToOccupation[$data['fk_role_id']])) {
            $occupation = $roleToOccupation[$data['fk_role_id']];
        }

        // Prepare user payload (match users table column names as in User.php fillable)
        $userPayload = [
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'suffix' => $data['suffix'] ?? '',
            // prefer explicit house_number if provided (frontend sets this before post)
            'house_number' => $data['house_number'] ?? ($data['house'] ?? null),
            'street' => $data['street'] ?? null,
            'phase' => $data['phase'] ?? null,
            'package' => $data['package'] ?? null,
            'barangay' => $data['barangay'] ?? ($data['barangay_input'] ?? null),
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
            'sex' => $data['sex'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'place_of_birth' => $data['place_of_birth'] ?? '',
            'religion' => $data['religion'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'occupation' => $occupation, // Use role-based occupation if not provided
            'civil_status' => $data['civil_status'] ?? null,
            'fk_role_id' => $data['fk_role_id'], // Required field, always set
            'created_at' => now(), // Set creation timestamp
            // 'profile_description' => null, // add if your form provides it
        ];

        DB::beginTransaction();

        try {
            // Create user (users table)
            $user = User::create($userPayload);

            // Set default email if not provided (since email column cannot be null)
            $email = $data['email'] ?? null;
            if (empty($email)) {
                // Generate a placeholder email based on contact number
                // Format: user{contact_number}@barangay176b.local
                $email = 'user' . $data['contact_number'] . '@barangay176b.local';
            }

            // Create credential linked to the user (user_credentials table)
            $credentialPayload = [
                'fk_user_id' => $user->user_id,
                'contact_number' => $data['contact_number'],
                'secondary_contact_number' => $data['secondary_contact_number'] ?? null,
                'email' => $email,
                'password' => $hashedPassword,
            ];

            UserCredential::create($credentialPayload);

            DB::commit();

            // Get the registered user's name for the success message
            $registeredName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            $roleName = $roleToOccupation[$data['fk_role_id']] ?? 'Official';

            // Redirect to dashboard with success message
            return redirect()->route('dashboard_admin')
                             ->with('success', "Official registered successfully! {$registeredName} has been registered as {$roleName} and can now access the system.");

        } catch (\Throwable $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('RegisterOfficialController::store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password']), // Don't log password
            ]);

            return back()->withInput()
                         ->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
