<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        // Check if phone number is already registered
        $isRegistered = \DB::table('registration_requests')
            ->where('contact_number', $phoneNumber)
            ->exists();

        if ($isRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered. Please use a different number or contact support.'
            ], 422);
        }

        $result = $this->otpService->sendOtp($phoneNumber);

        if ($result['success']) {
            // Store verification session key to track that OTP was sent
            $sessionKey = "otp_sent:{$phoneNumber}";
            Cache::put($sessionKey, true, now()->addMinutes(10));
            
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 422);
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
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

        $isValid = $this->otpService->verifyOtp($phoneNumber, $otpCode);

        if ($isValid) {
            // Mark phone number as verified in cache (valid for 30 minutes)
            $verifiedKey = "phone_verified:{$phoneNumber}";
            Cache::put($verifiedKey, true, now()->addMinutes(30));
            
            Log::info('Phone number verified via OTP', [
                'phone_number' => $phoneNumber,
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code. Please check and try again.'
        ], 422);
    }

    /**
     * Check if phone number is verified (for registration submission)
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        $verifiedKey = "phone_verified:{$phoneNumber}";
        $isVerified = Cache::has($verifiedKey);

        return response()->json([
            'verified' => $isVerified
        ]);
    }
}

