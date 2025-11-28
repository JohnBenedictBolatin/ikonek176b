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
        $validated = $request->validate([
            'user_cred_id' => ['required', 'integer', 'exists:user_credentials,user_cred_id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'], // <-- min:6
            // require digits only; allow between 7 and 15 digits (adjust if you need different range)
            'contact_number' => ['nullable', 'digits_between:7,15'],
            'secondary_contact' => ['nullable', 'digits_between:7,15'],
        ]);

        $cred = UserCredential::find($validated['user_cred_id']);

        if (! $cred) {
            return back()->withErrors(['user_cred_id' => 'Selected account not found.']);
        }

        // Hash and save password
        $cred->password = Hash::make($validated['password']);

        // Update contact numbers (store as integers/strings consistent with DB column)
        if ($request->filled('contact_number')) {
            // store as integer-compatible value; cast to string if your column is unsignedBigInt but you prefer to store as numeric string
            $cred->contact_number = $request->input('contact_number');
        }
        if ($request->filled('secondary_contact')) {
            $cred->secondary_contact_number = $request->input('secondary_contact');
        }

        $cred->save();

        return back()->with('success', 'Password updated successfully.');
    }
}
