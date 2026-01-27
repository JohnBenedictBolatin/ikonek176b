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
        $startTime = microtime(true);
        
        $request->validate([
            'phone_number' => ['required', 'regex:/^[0-9]{10,11}$/'],
        ]);

        $phoneNumber = preg_replace('/\D+/', '', $request->phone_number);
        
        // Normalize: if 10 digits starting with 9, add leading 0
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '9') {
            $phoneNumber = '0' . $phoneNumber;
        }

        $validationTime = microtime(true) - $startTime;
        Log::info('OTP Request Started', [
            'phone_number' => $phoneNumber,
            'validation_time_ms' => round($validationTime * 1000, 2)
        ]);

        // Check cache first to avoid database queries if possible
        $cacheKey = "otp_user_check:{$phoneNumber}";
        $cachedCheck = Cache::get($cacheKey);
        
        $dbCheckStart = microtime(true);
        
        if ($cachedCheck === null) {
            // Quick check: Is phone number already registered? (optimized query)
            $hasActiveUser = DB::table('user_credentials')
                ->where('contact_number', $phoneNumber)
                ->orWhere('secondary_contact_number', $phoneNumber)
                ->exists();

            // Cache the result for 5 minutes
            Cache::put($cacheKey, $hasActiveUser ? 'registered' : 'available', now()->addMinutes(5));
        } else {
            $hasActiveUser = ($cachedCheck === 'registered');
        }

        $dbCheckTime = microtime(true) - $dbCheckStart;

        if ($hasActiveUser) {
            Log::info('OTP Request Blocked - User Exists', [
                'phone_number' => $phoneNumber,
                'db_check_time_ms' => round($dbCheckTime * 1000, 2),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered. Please use a different number or contact support.'
            ], 422);
        }

        // Quick check: Is there a pending registration request?
        $pendingCheckStart = microtime(true);
        $hasPendingRequest = \App\Models\RegistrationReq::where(function($query) use ($phoneNumber) {
            $query->where('contact_number', $phoneNumber)
                  ->orWhere('secondary_contact_number', $phoneNumber);
        })
        ->where('registration_status', 'Pending')
        ->exists();
        $pendingCheckTime = microtime(true) - $pendingCheckStart;

        if ($hasPendingRequest) {
            Log::info('OTP Request Blocked - Pending Request', [
                'phone_number' => $phoneNumber,
                'pending_check_time_ms' => round($pendingCheckTime * 1000, 2),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'This phone number has a pending registration request. Please wait for approval or contact support.'
            ], 422);
        }

        $preOtpTime = microtime(true) - $startTime;
        Log::info('OTP Pre-checks Complete', [
            'phone_number' => $phoneNumber,
            'db_check_time_ms' => round($dbCheckTime * 1000, 2),
            'pending_check_time_ms' => round($pendingCheckTime * 1000, 2),
            'pre_otp_total_ms' => round($preOtpTime * 1000, 2)
        ]);

        try {
            $otpStartTime = microtime(true);
            $result = $this->otpService->sendOtp($phoneNumber);
            $otpTime = microtime(true) - $otpStartTime;

            if ($result['success']) {
                // Store verification session key to track that OTP was sent
                $sessionKey = "otp_sent:{$phoneNumber}";
                Cache::put($sessionKey, true, now()->addMinutes(10));
                
                $totalTime = microtime(true) - $startTime;
                Log::info('OTP Sent Successfully', [
                    'phone_number' => $phoneNumber,
                    'otp_service_time_ms' => round($otpTime * 1000, 2),
                    'total_time_ms' => round($totalTime * 1000, 2)
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            $totalTime = microtime(true) - $startTime;
            Log::warning('OTP Send Failed', [
                'phone_number' => $phoneNumber,
                'otp_service_time_ms' => round($otpTime * 1000, 2),
                'total_time_ms' => round($totalTime * 1000, 2),
                'error_message' => $result['message'] ?? 'Unknown error'
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        } catch (\Exception $e) {
            $totalTime = microtime(true) - $startTime;
            Log::error('OtpController::send exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber,
                'total_time_ms' => round($totalTime * 1000, 2)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending OTP. Please try again.'
            ], 500);
        }
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

