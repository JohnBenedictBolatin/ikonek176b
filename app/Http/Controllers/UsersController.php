<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Models\user_credentials;

class UsersController extends Controller
{
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'role_id' => 'required|integer|exists:role_categories,id',
        //     'last_name'           => 'required|string|max:50',
        //     'first_name'          => 'required|string|max:50',
        //     'middle_name'         => 'nullable|string|max:50',
        //     'suffix'              => 'nullable|string|max:50',
        //     'birthdate'           => 'nullable|date',
        //     'sex'                 => 'nullable|in:Male,Female,Other',
        //     'civil_status'        => 'nullable|string|max:50',
        //     'role'                => 'nullable', 
        //     'contact_number'      => 'required|string|max:20|unique:users,contact_number',
        //     'secondary_number'    => 'nullable|string|max:20',
        //     'password'            => 'required|string|min:6',
        //     'home_address'        => 'nullable|string|max:255',
        //     'phase'               => 'nullable|in:1,2,3,4,5,6',
        //     'package'             => 'nullable|in:1,2',
        //     'proof_of_residency'  => 'nullable|string|max:255',
        //     'profile_description' => 'nullable|string',
        // ]);

        // users::create($validated);

        // Resolve role -> role_id
        // Prefer role_id from the form; fall back to role (name) resolution
        // $roleId = null;

        // if (!empty($validated['role_id'])) {
        //     $roleId = is_numeric($validated['role_id']) ? (int) $validated['role_id'] : null;
        //     if ($roleId && ! DB::table('role_categories')->where('id', $roleId)->exists()) {
        //         $roleId = null;
        //     }
        // } elseif (!empty($validated['role'])) {
        //     $roleInput = $validated['role'];
        //     if (is_numeric($roleInput)) {
        //         $roleId = (int) $roleInput;
        //         if (! DB::table('role_categories')->where('id', $roleId)->exists()) {
        //             $roleId = null;
        //         }
        //     } else {
        //         $roleRow = DB::table('role_categories')
        //             ->where('role_name', $roleInput)
        //             ->orWhere('name', $roleInput)
        //             ->first();

        //         if ($roleRow) {
        //             $roleId = $roleRow->id;
        //         }
        //     }
        // }

        // $roleId = (int) $validated['role_id'];

        // users::create([
        //     'role_id'             => $roleId,
        //     'last_name'           => $validated['last_name'],
        //     'first_name'          => $validated['first_name'],
        //     'middle_name'         => $validated['middle_name'] ?? null,
        //     'suffix'              => $validated['suffix'] ?? null,
        //     'birthdate'           => $validated['birthdate'] ?? null,
        //     'sex'                 => $validated['sex'] ?? null,
        //     'civil_status'        => $validated['civil_status'] ?? null,
        //     'contact_number'      => $validated['contact_number'],
        //     'secondary_number'    => $validated['secondary_number'] ?? null,
        //     'password'            => Hash::make($validated['password']),
        //     'home_address'        => $validated['home_address'] ?? null,
        //     // 'phase'               => $validated['phase'] ?? null,
        //     // 'package'             => $validated['package'] ?? null,
        //     'proof_of_residency'  => $validated['proof_of_residency'] ?? null,
        //     'profile_description' => $validated['profile_description'] ?? null,
        //     'date_joined'         => now(),
        // ]);

        // return redirect()->back()->with('success', 'Registration successful.');

        $data = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'birthdate' => 'nullable|date',
            'sex' => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'fk_role_id' => 'required|integer',
            'home_address' => 'nullable|string|max:500',


            'contact_number' => 'required|string|min:10|max:15',
            'secondary_number' => 'nullable|string|min:10|max:15',
            'password' => 'required|string|min:6', // expects `password_confirmation` from client
        ]);


        DB::beginTransaction();
        try {
            $user = users::create([
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'suffix' => $data['suffix'] ?? null,
            'sex' => $data['sex'] ?? null,
            'civil_status' => $data['civil_status'] ?? null,
            'home_address' => $data['home_address'] ?? null,
            'fk_role_id' => $data['fk_role_id'],
            ]);


            $credential = user_credentials::create([
            'fk_user_id' => $user->id,
            'contact_number' => $data['contact_number'],
            'secondary_contact_number' => $data['secondary_number'] ?? null,
            'password' => Hash::make($data['password']),
            'is_approved' => false,
            'requested_at' => now(),
            ]);


            DB::commit();


            // Return success Inertia response — you could redirect to a "Request Received" page
            return redirect()->back()->with('success','Registration request submitted. Await admin approval.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}
