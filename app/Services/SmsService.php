<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SMS Service - Factory/Facade for SMS providers
 * 
 * Supports multiple providers:
 * - semaphore: Semaphore.co (Philippine SMS gateway)
 * - sms_gateway: SMS Gateway app (Android-based SMS gateway)
 * 
 * Configure provider in .env:
 * SMS_PROVIDER=semaphore  (or sms_gateway)
 */
class SmsService
{
    private SmsServiceInterface $provider;
    private string $providerName;

    public function __construct()
    {
        $this->providerName = config('services.sms.provider', 'sms_gateway');
        
        Log::info('SmsService initialized', [
            'provider_from_config' => config('services.sms.provider'),
            'provider_name' => $this->providerName,
            'env_sms_provider' => env('SMS_PROVIDER'),
        ]);
        
        $this->provider = $this->createProvider($this->providerName);
    }

    /**
     * Create the appropriate SMS provider instance
     */
    private function createProvider(string $providerName): SmsServiceInterface
    {
        switch ($providerName) {
            case 'semaphore':
                return new SemaphoreService();
            case 'sms_gateway':
            default:
                return new SmsGatewayService();
        }
    }

    /**
     * Get the current provider name
     */
    public function getProviderName(): string
    {
        return $this->provider->getProviderName();
    }

    /**
     * Send an SMS message
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits for PH)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        Log::info('SmsService::sendSms called', [
            'provider' => $this->providerName,
            'phone' => $phoneNumber,
            'message_length' => strlen($message)
        ]);

        return $this->provider->sendSms($phoneNumber, $message);
    }

    /**
     * Send OTP to phone number
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits)
     * @return array ['success' => bool, 'message' => string, 'code' => string|null]
     */
    public function sendOtp(string $phoneNumber): array
    {
        Log::info('=== SmsService::sendOtp called ===', [
            'provider' => $this->providerName,
            'phone' => $phoneNumber,
            'timestamp' => now()->toDateTimeString()
        ]);

        // Check rate limiting (max 10 OTP requests per hour per number)
        $rateLimitKey = "otp_rate_limit:{$phoneNumber}";
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= 10) {
            Log::warning('OTP rate limit exceeded', [
                'phone' => $phoneNumber,
                'attempts' => $attempts
            ]);
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try again in an hour.',
                'code' => null
            ];
        }

        // Use the provider's dedicated sendOtp method if available (e.g., Semaphore OTP endpoint)
        // Otherwise fall back to sending OTP via regular SMS
        $result = null;
        $otpCode = null;

        if ($this->provider instanceof SemaphoreService) {
            // Semaphore has a dedicated OTP endpoint that auto-generates the code
            $result = $this->provider->sendOtp($phoneNumber);
            $otpCode = $result['code'] ?? null;
        } elseif ($this->provider instanceof SmsGatewayService) {
            // SMS Gateway uses regular SMS with our own generated code
            $result = $this->provider->sendOtp($phoneNumber);
            $otpCode = $result['code'] ?? null;
        } else {
            // Fallback: generate OTP and send via regular SMS
            $otpCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $message = "Your One Time Password for iKonek176B registration is: {$otpCode}. Please use it within 5 minutes.";
            $result = $this->provider->sendSms($phoneNumber, $message);
            if ($result['success']) {
                $result['code'] = $otpCode;
            }
        }

        if ($result['success'] && $otpCode) {
            // Store OTP in cache with 5 minute expiration
            $cacheKey = "otp:{$phoneNumber}";
            Cache::put($cacheKey, $otpCode, now()->addMinutes(5));

            // Increment rate limit counter
            Cache::put($rateLimitKey, $attempts + 1, now()->addHour());

            Log::info('OTP sent successfully', [
                'provider' => $this->providerName,
                'phone_number' => $phoneNumber,
                'message_id' => $result['message_id'] ?? null
            ]);

            return [
                'success' => true,
                'message' => $result['message'],
                'code' => $otpCode,
                'message_id' => $result['message_id'] ?? null,
                'status' => $result['status'] ?? null
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send OTP. Please try again.',
            'code' => null
        ];
    }

    /**
     * Verify OTP code for phone number
     * 
     * @param string $phoneNumber Phone number
     * @param string $otpCode OTP code to verify
     * @return bool
     */
    public function verifyOtp(string $phoneNumber, string $otpCode): bool
    {
        $cacheKey = "otp:{$phoneNumber}";
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp) {
            return false;
        }

        // Compare OTP codes (as strings to handle leading zeros)
        if ((string)$storedOtp === (string)$otpCode) {
            // OTP verified, remove it from cache
            Cache::forget($cacheKey);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP exists for phone number (not expired)
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public function hasOtp(string $phoneNumber): bool
    {
        $cacheKey = "otp:{$phoneNumber}";
        return Cache::has($cacheKey);
    }

    /**
     * Format phone number using the current provider's format
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    public function formatPhoneNumber(string $phoneNumber): ?string
    {
        return $this->provider->formatPhoneNumber($phoneNumber);
    }
}



