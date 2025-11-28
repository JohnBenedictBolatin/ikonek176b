<?php

namespace App\Http\Controllers;

use App\Models\RegistrationReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RegisterOfficialController extends Controller
{
    public function store(Request $request)
    {
        // Basic server-side validation. Adjust rules to your needs.
        $data = $request->validate([
            'last_name' => ['required','string','max:255'],
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'suffix' => ['nullable','string','max:50'],

            // contact numbers: expect 10 digits (without +63 in UI). We'll prepend +63 server-side.
            'contact_number' => ['required','digits:10', Rule::unique('registration_req','contact_number')],
            'secondary_contact_number' => ['nullable','digits:10'],
            'phase' => ['nullable','string'],
            'package' => ['nullable','string'],

            'password' => ['required','string','min:6'],
            'sex' => ['nullable','string','max:50'],
            'birthdate' => ['nullable','date'],
            'civil_status' => ['nullable','string','max:50'],
            'fk_role_id' => ['nullable','integer'],

            'address' => ['nullable','string'],
            // 'barangay' => ['nullable','string'],
            
            // 'proof_of_residency' => ['nullable','string'],
        ]);

        // --- DEFAULT SUFFIX: if suffix not provided or empty, set to "unless" ---
        if (empty($data['suffix'])) {
            $data['suffix'] = '';
        }

        // Normalize phone numbers: store them as +63XXXXXXXXXX
        if (!empty($data['contact_number'])) {
            // ensure digits only, then prepend +63
            $contact = preg_replace('/\D+/', '', $data['contact_number']);
            if (Str::startsWith($contact, '0')) {
                $contact = ltrim($contact, '0'); // remove leading 0 if user included it
            }
            $data['contact_number'] = $contact;
        }

        if (!empty($data['secondary_contact_number'])) {
            $secondary = preg_replace('/\D+/', '', $data['secondary_contact_number']);
            if (Str::startsWith($secondary, '0')) {
                $secondary = ltrim($secondary, '0');
            }
            $data['secondary_contact_number'] = $secondary;
        }

        // Trim phase/package (in case the UI sends extra whitespace)
        if (!empty($data['phase'])) {
            $data['phase'] = trim($data['phase']);
        }
        if (!empty($data['package'])) {
            $data['package'] = trim($data['package']);
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // default registration status
        $data['registration_status'] = 'Pending';

        unset($data['barangay'], $data['proof_of_residency']);

        // created_at will be set automatically by Eloquent (migration uses current timestamp)
        $registration = RegistrationReq::create($data);

        // Return a proper Inertia/JSON response. Inertia form.post expects a redirect or success.
        // You can choose to redirect to login with a success flash message:
        return redirect()->route('login_admin')->with('success', 'Registration request submitted. Please wait for approval.');
    }

    
}