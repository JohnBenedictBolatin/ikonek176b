<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HelpCenterController extends Controller
{
    public function submitMessage(Request $request)
    {
        // Check if form data is provided (from landing page contact form)
        // If form fields are provided, they are required
        $hasFormData = $request->filled('first_name') || $request->filled('last_name') || $request->filled('email');
        
        $rules = [
            'message' => 'required|string|min:10|max:2000',
        ];
        
        if ($hasFormData) {
            // Form data provided (from landing page) - require these fields
            $rules['first_name'] = 'required|string|max:255';
            $rules['last_name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
        } else {
            // No form data (from help center) - these are optional
            $rules['first_name'] = 'nullable|string|max:255';
            $rules['last_name'] = 'nullable|string|max:255';
            $rules['email'] = 'nullable|email|max:255';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // Check if form data is provided (from landing page contact form)
        // If form fields are provided, use them regardless of authentication status
        $hasFormData = $request->filled('first_name') || $request->filled('last_name') || $request->filled('email');
        
        $firstName = null;
        $lastName = null;
        $userName = 'Guest';
        $userEmail = null;
        $userContact = null;
        $userType = null;

        if ($hasFormData) {
            // Form data provided (from landing page) - use form data
            $firstName = $request->first_name ?? null;
            $lastName = $request->last_name ?? null;
            $userName = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
            if (empty($userName)) {
                $userName = 'Guest';
            }
            $userEmail = $request->email ?? null;
            // For form submissions, user_type is null (Guest)
            $userType = null;
        } elseif ($user) {
            // Authenticated user submitting from help center - get info from user record
            $firstName = $user->first_name ?? null;
            $lastName = $user->last_name ?? null;
            $userName = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
            if (empty($userName)) {
                $userName = 'Guest';
            }
            $credential = $user->credential;
            $userEmail = $credential->email ?? null;
            $userContact = $credential->contact_number ?? null;
            $userType = $user->fk_role_id == 1 ? 'Resident' : 'Employee';
        } else {
            // Guest user - no form data and not authenticated (shouldn't happen with validation)
            $userName = 'Guest';
        }

        ContactMessage::create([
            'user_id' => $user ? $user->user_id : null,
            'message' => $request->message,
            'user_name' => $userName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'user_email' => $userEmail,
            'user_contact_number' => $userContact,
            'user_type' => $userType,
            'status' => 'New',
        ]);

        return back()->with('success', 'Thank you for your message! We will get back to you as soon as possible.');
    }
}
