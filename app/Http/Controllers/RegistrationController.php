<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Http\Requests\RegisterRequest;
use App\Models\users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\user_credentials;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validated();

        // wrap in DB transaction: create user then credential
        DB::beginTransaction();
        try {
            $user = users::create([
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'suffix' => $data['suffix'] ?? null,
                'sex' => $data['sex'],
                'civil_status' => $data['civil_status'],
                'address' => $data['home_address'],
                'profile_description' => null,
                // set a default role id for residents, change as needed
                'fk_role_id' => $request->input('fk_role_id', 2),
            ]);


            // user_credentials::create([
            //     'fk_user_id' => $user->user_id,
            //     'contact_number' => $data['contact_number'],
            //     'secondary_contact_number' => $data['secondary_number'] ?? null,
            //     'password' => Hash::make($data['password']),
            // ]);


            DB::commit();


            // redirect to login page with a flash message
            return redirect()->route('login')->with('success', 'Registration submitted. Please check messages for approval.');
        } catch (\Throwable $e) {
            DB::rollBack();


            // log exception if you want: Log::error($e->getMessage());
            return back()->withErrors(['server' => 'Unable to process registration at this time.'])->withInput();
        }

        // $validated = $request->validate([
        //     'fk_role_id' => 'nullable|integer',
        //     'last_name' => 'required|string|max:255',
        //     'first_name' => 'required|string|max:255',
        //     'middle_name' => 'nullable|string|max:255',
        //     'suffix' => 'nullable|string|max:50',
        //     'birthdate' => 'required|date',
        //     'sex' => 'required|string|max:50',
        //     'civil_status' => 'required|string|max:50',
        //     'home_address' => 'required|string|max:1000',
        //     'barangay' => 'nullable|string|max:255',
        //     'phase' => 'nullable|string|max:50',
        //     'package' => 'nullable|string|max:50',
        //     'proof_of_residency' => 'required|string|max:255',
        //     'contact_number' => 'required|string|max:20',
        //     'secondary_number' => 'nullable|string|max:20',
        //     'password' => 'required|string|min:6',
        // ]);

        // // create user record and capture the created model
        // $user = users::create([
        //     'fk_role_id' => $validated['fk_role_id'] ?? null,
        //     'last_name' => $validated['last_name'],
        //     'first_name' => $validated['first_name'],
        //     'middle_name' => $validated['middle_name'] ?? null,
        //     'suffix' => $validated['suffix'] ?? null,
        //     'birthdate' => $validated['birthdate'] ?? null,
        //     'sex' => $validated['sex'] ?? null,
        //     'civil_status' => $validated['civil_status'] ?? null,
        //     'home_address' => $validated['home_address'] ?? null,
        //     'barangay' => $validated['barangay'] ?? null,
        //     'phase' => $validated['phase'] ?? null,
        //     'package' => $validated['package'] ?? null,
        //     'proof_of_residency' => $validated['proof_of_residency'] ?? null,
        //     'contact_number' => $validated['contact_number'] ?? null,
        //     'secondary_contact' => $validated['secondary_number'] ?? null,
        //     'password' => Hash::make($validated['password']),
        // ]);

        // // store registration request with hashed password and link to created user
        // // RegistrationController -> use Eloquent, matching model fillable:
        // user_credentials::create([
        //     'fk_user_id' => $user->id,
        //     'contact_number' => $validated['contact_number'] ?? null,
        //     'secondary_contact_number' => $validated['secondary_number'] ?? null,
        //     'password' => Hash::make($validated['password']),
        //     'is_approved' => false,
        //     'requested_at' => now(),
        // ]);


        // return back()->with('success','Registration submitted and pending admin approval.');
    }
}
