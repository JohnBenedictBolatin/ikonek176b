<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function find(Request $request)
    {
        $last = $request->input('last_name');
        $first = $request->input('first_name');
        $middle = $request->input('middle_name');
        $suffix = $request->input('suffix');
        $contact = $request->input('contact_number');

        $query = \DB::table('users')
            ->join('user_credentials', 'users.user_id', '=', 'user_credentials.fk_user_id')
            ->select(
                'users.user_id',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.suffix',
                'user_credentials.user_cred_id',
                'user_credentials.contact_number',
                'user_credentials.secondary_contact_number'
            );

        if ($last) {
            $query->whereRaw('LOWER(users.last_name) LIKE ?', [strtolower($last) . '%']);
        }
        if ($first) {
            $query->whereRaw('LOWER(users.first_name) LIKE ?', [strtolower($first) . '%']);
        }
        if ($middle) {
            $query->whereRaw('LOWER(users.middle_name) LIKE ?', [strtolower($middle) . '%']);
        }
        if ($suffix) {
            $query->where('users.suffix', $suffix);
        }
        if ($contact) {
            // We assume the frontend provides only digits (no +63). Match prefix.
            $query->where('user_credentials.contact_number', 'like', $contact . '%');
        }

        $results = $query->limit(10)->get();

        return response()->json($results);
    }

    public function update(Request $request)
    {
        // Validation adjusted:
        // - password minimum length 6 and must be confirmed
        // - contact fields must be digits (nullable)
        // - optional name fields accepted for verification
        $validated = $request->validate([
            'user_cred_id' => ['required', 'integer', 'exists:user_credentials,user_cred_id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'contact_number' => ['nullable', 'digits_between:7,15'],
            'secondary_contact' => ['nullable', 'digits_between:7,15'],
            'last_name' => ['nullable', 'string'],
            'first_name' => ['nullable', 'string'],
            'middle_name' => ['nullable', 'string'],
            'suffix' => ['nullable', 'string'],
        ]);

        $cred = UserCredential::with('user')->find($validated['user_cred_id']);

        if (! $cred) {
            return back()->withErrors(['user_cred_id' => 'Selected account not found.']);
        }

        // Verify provided identifying fields actually match the selected credential's user record.
        // For each provided field, check case-insensitive equality (or prefix for phone).
        $user = $cred->user;

        if ($request->filled('last_name')) {
            if (! $user || strcasecmp(trim($user->last_name ?? ''), trim($request->input('last_name'))) !== 0) {
                return back()->withErrors(['last_name' => 'Last name does not match the selected account.']);
            }
        }
        if ($request->filled('first_name')) {
            if (! $user || strcasecmp(trim($user->first_name ?? ''), trim($request->input('first_name'))) !== 0) {
                return back()->withErrors(['first_name' => 'First name does not match the selected account.']);
            }
        }
        if ($request->filled('middle_name')) {
            if (! $user || strcasecmp(trim($user->middle_name ?? ''), trim($request->input('middle_name'))) !== 0) {
                return back()->withErrors(['middle_name' => 'Middle name does not match the selected account.']);
            }
        }
        if ($request->filled('suffix')) {
            if (! $user || strcasecmp(trim($user->suffix ?? ''), trim($request->input('suffix'))) !== 0) {
                return back()->withErrors(['suffix' => 'Suffix does not match the selected account.']);
            }
        }

        // For contact number verification, allow frontend matching behavior: check that credential's stored number starts with provided digits.
        if ($request->filled('contact_number')) {
            $provided = preg_replace('/\D+/', '', $request->input('contact_number'));
            $stored = preg_replace('/\D+/', '', $cred->contact_number ?? '');
            if ($provided === '' || strpos($stored, $provided) !== 0) {
                return back()->withErrors(['contact_number' => 'Contact number does not match the selected account.']);
            }
        }

        // All good: hash and save new password
        $cred->password = Hash::make($validated['password']);

        // Update contact numbers when provided (overwrite)
        if ($request->filled('contact_number')) {
            $cred->contact_number = $request->input('contact_number');
        }
        if ($request->filled('secondary_contact')) {
            $cred->secondary_contact_number = $request->input('secondary_contact');
        }

        $cred->save();

        // return back()->with('success', 'Password updated successfully.');
        return redirect()->route('login')->with('success', 'Password updated successfully. Please login.');

    }
}
