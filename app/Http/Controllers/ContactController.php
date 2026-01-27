<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            ContactMessage::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_email' => $request->email,
                'message' => $request->message,
                'user_name' => $request->first_name . ' ' . $request->last_name,
                'status' => 'New', // Use 'New' to match the enum values: 'New', 'Read', 'Replied'
                'user_type' => null, // Guest users don't have a user_type
            ]);

            return back()->with('success', 'Thank you for your message! We will get back to you as soon as possible.');
        } catch (\Exception $e) {
            \Log::error('Error creating contact message: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['message' => 'An error occurred while submitting your message. Please try again.'])->withInput();
        }
    }
}



