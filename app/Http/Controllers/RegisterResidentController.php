<?php

namespace App\Http\Controllers;

use App\Models\RegistrationReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RegisterResidentController extends Controller
{

    public function store(Request $request)
    {
        // Optional debug
        \Log::info('register resident attempt', ['ip' => $request->ip()]);

        // Validate incoming form. All fields required except middle_name and suffix.
        $data = $request->validate([
            'last_name' => ['required','string','max:255'],
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'suffix' => ['nullable','string','max:50'],

            // contact numbers: expect 10 or 11 digits. Unique on contact_number.
            'contact_number' => ['required','regex:/^[0-9]{10,11}$/', Rule::unique('registration_requests','contact_number')],

            'phase' => ['required','string','max:50'],
            'package' => ['required','string','max:50'],

            'password' => ['required','string','min:6'],
            'sex' => ['required','string','max:50'],
            'birthdate' => ['required','date'],
            'civil_status' => ['required','string','max:50'],
            'fk_role_id' => ['nullable','integer'],

            // address pieces
            'address' => ['nullable','string','max:1000'],
            'house_number' => ['required','string','max:255'],
            'street' => ['required','string','max:255'],
            'barangay' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'province' => ['nullable','string','max:255'],

            // additional personal info
            'religion' => ['required','string','max:255'],
            'nationality' => ['required','string','max:255'],
            'occupation' => ['required','string','max:255'],

            // proof file (image) - required
            'proof' => ['required','image','max:10240'],
            'proof_of_residency' => ['required','string','max:255'],
        ]);

        // -----------------------
        // Normalize phone numbers
        // -----------------------
        // Helper: normalize to digits only; keep leading 0 for 11-digit numbers
        $normalizePhone = function ($value) {
            if (is_null($value) || $value === '') return null;
            $digits = preg_replace('/\D+/', '', $value);
            if ($digits === '') return null;
            // Keep the number as is (should be 11 digits starting with 09)
            // If it's 10 digits starting with 9, add leading 0
            if (strlen($digits) === 10 && $digits[0] === '9') {
                $digits = '0' . $digits;
            }
            return $digits === '' ? null : $digits;
        };

        $data['contact_number'] = $normalizePhone($data['contact_number'] ?? null);

        // If contact_number ended up null (shouldn't happen since it's required), throw validation-like response
        if (empty($data['contact_number'])) {
            return back()->withErrors(['contact_number' => 'Invalid contact number provided.']);
        }

        // Check if phone number is verified via OTP
        $verifiedKey = "phone_verified:{$data['contact_number']}";
        $isVerified = Cache::has($verifiedKey);
        
        if (!$isVerified) {
            \Log::warning('Registration attempt without OTP verification', [
                'contact_number' => $data['contact_number'],
                'ip' => $request->ip()
            ]);
            return back()->withErrors(['contact_number' => 'Please verify your phone number with OTP before submitting registration.'])->withInput();
        }

        // -----------------------
        // Ensure personal fields exist (avoid NOT NULL DB errors)
        // -----------------------
        $data['religion'] = $data['religion'] ?? '';
        $data['nationality'] = $data['nationality'] ?? '';
        $data['occupation'] = $data['occupation'] ?? '';

        // Ensure suffix exists (optional field, default to empty string if not provided)
        if (!isset($data['suffix'])) $data['suffix'] = '';

        // -----------------------
        // Hash password
        // -----------------------
        $data['password'] = Hash::make($data['password']);

        // default registration status
        $data['registration_status'] = 'Pending';

        // -----------------------
        // Handle file upload (if provided)
        // -----------------------
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            if ($file && $file->isValid()) {
                // Store file in public storage, similar to document request attachments
                $proofPath = $file->store('registration_proofs', 'public');
            }
        }
        
        // Store proof_of_residency (document type) in proof column if no file uploaded
        // If file is uploaded, store file path; otherwise store document type
        if ($proofPath) {
            $data['proof'] = $proofPath;
        } else {
            $data['proof'] = $data['proof_of_residency'] ?? null;
        }

        // created_at (table has created_at but not necessarily updated_at)
        $data['created_at'] = now();

        // -----------------------
        // Prepare final data for insertion
        // -----------------------
        $saveData = [
            'last_name' => $data['last_name'] ?? '',
            'first_name' => $data['first_name'] ?? '',
            'middle_name' => $data['middle_name'] ?? '',
            'suffix' => $data['suffix'] ?? '',

            // DB expects numeric/integer - if null, pass null (so INSERT uses NULL)
            // Contact numbers stored as strings of digits, but column type may be INT. We pass null when missing.
            'contact_number' => $data['contact_number'],
            'secondary_contact_number' => null,

            'email' => '', // Set to empty string since column doesn't allow null
            'password' => $data['password'] ?? null,

            'house_number' => $data['house_number'] ?? null,
            'street' => $data['street'] ?? null,
            'phase' => $data['phase'] ?? null,
            'package' => $data['package'] ?? null,
            'barangay' => $data['barangay'] ?? null,
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,

            // personal info fields
            'place_of_birth' => null,
            'religion' => $data['religion'],
            'nationality' => $data['nationality'],
            'occupation' => $data['occupation'],

            'sex' => $data['sex'] ?? null,
            // 'birthdate' may be null; controller leaves it null when not provided
            'birthdate' => $data['birthdate'] ?? null,
            'civil_status' => $data['civil_status'] ?? null,

            // fk_role_id: ensure integer or default to 1
            'fk_role_id' => isset($data['fk_role_id']) && is_numeric($data['fk_role_id']) ? intval($data['fk_role_id']) : 1,

            'proof' => $data['proof'] ?? null,
            'registration_status' => $data['registration_status'] ?? 'Pending',
            'created_at' => $data['created_at'],
        ];

        // -----------------------
        // Insert record
        // -----------------------
        $registration = RegistrationReq::create($saveData);

        \Log::info('Registration request created successfully', [
            'id' => $registration->id,
            'contact_number' => $registration->contact_number,
            'name' => $registration->first_name . ' ' . $registration->last_name
        ]);

        // For Inertia, use Inertia redirect or regular redirect with flash message
        return redirect()->route('login')->with('success', 'Registration request submitted successfully! Please wait 1-3 working days for approval to be sent via SMS.');
    }
}
