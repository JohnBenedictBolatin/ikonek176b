<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use App\Models\UserCredential;
use App\Models\AdminCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number for password reset
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        // Check if phone number exists in user_credentials or admin_credentials
        $userCredential = UserCredential::where('contact_number', $phoneNumber)
            ->orWhere('secondary_contact_number', $phoneNumber)
            ->first();

        // For admin credentials, we need to check if they have a phone number field
        // Since admin credentials use username, we'll check if the phone matches any user's contact
        // For now, we'll focus on user_credentials as that's where phone numbers are stored
        
        if (!$userCredential) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found. Please check your number or contact support.'
            ], 422);
        }

        // Send OTP
        try {
            $result = $this->otpService->sendOtp($phoneNumber);

            if ($result['success']) {
                // Store reset session key to track that OTP was sent for password reset
                $sessionKey = "password_reset_otp_sent:{$phoneNumber}";
                Cache::put($sessionKey, true, now()->addMinutes(10));
                
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to your mobile number.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send OTP. Please try again.'
            ], 422);
        } catch (\Exception $e) {
            Log::error('ForgotPasswordController::sendOtp exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP code for password reset
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        $otpCode = $request->otp_code;

        // Check if OTP was sent for password reset
        $sessionKey = "password_reset_otp_sent:{$phoneNumber}";
        if (!Cache::has($sessionKey)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP session expired. Please request a new OTP.'
            ], 422);
        }

        $isValid = $this->otpService->verifyOtp($phoneNumber, $otpCode);

        if ($isValid) {
            // Mark phone number as verified for password reset (valid for 15 minutes)
            $verifiedKey = "password_reset_verified:{$phoneNumber}";
            Cache::put($verifiedKey, true, now()->addMinutes(15));
            
            // Remove the OTP sent flag
            Cache::forget($sessionKey);
            
            Log::info('Phone number verified for password reset', [
                'phone_number' => $phoneNumber,
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully. You can now set a new password.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code. Please check and try again.'
        ], 422);
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        // Check if phone number is verified for password reset
        $verifiedKey = "password_reset_verified:{$phoneNumber}";
        if (!Cache::has($verifiedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification expired. Please start the password reset process again.'
            ], 422);
        }

        // Find user credential by phone number
        $userCredential = UserCredential::where('contact_number', $phoneNumber)
            ->orWhere('secondary_contact_number', $phoneNumber)
            ->first();

        if (!$userCredential) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please contact support.'
            ], 422);
        }

        // Update password
        try {
            DB::beginTransaction();
            
            $userCredential->password = Hash::make($request->password);
            $userCredential->save();
            
            // Clear verification cache
            Cache::forget($verifiedKey);
            
            DB::commit();
            
            Log::info('Password reset successful', [
                'phone_number' => $phoneNumber,
                'user_cred_id' => $userCredential->user_cred_id,
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. You can now login with your new password.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Password reset failed', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again or contact support.'
            ], 500);
        }
    }
}

