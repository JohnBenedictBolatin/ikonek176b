<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register_resident(Request $request) {
        // Validate
        $fields = $request->validate([
            'lName'       => ['required', 'string', 'max:255'],
            'fName'       => ['required', 'string', 'max:255'],
            'mName'       => ['string', 'max:255'],
            'suffix'      => ['string', 'max:255'],
            'dob'         => ['required', 'date'],          
            'sex'         => ['required', 'in:male,female'],
            'civilStatus' => ['required', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:255'],
            'fContact'    => ['required', 'string', 'max:255'],
            'sContact'    => ['string', 'max:255'],
            'otp'         => ['required', 'digits:6'],      
            'homeAdd'     => ['required', 'string', 'max:255'],
            'phase'       => ['required', 'string', 'max:255'],
            'package'     => ['required', 'string', 'max:255'],
            'idType'      => ['required', 'string', 'max:255'],
        ]);

        // Register
        $user = users::create($fields);

        // Login
        // Auth::login($user);

        return redirect()->route('discussion_resident');

    }
}
