<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * OTP Service - Wrapper around SmsService for OTP functionality
 * 
 * This class maintains backward compatibility with existing code
 * while using the new SmsService for actual SMS delivery.
 */
class OtpService
{
    private SmsService $smsService;

    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Generate and send OTP to phone number
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits)
     * @return array ['success' => bool, 'message' => string, 'code' => int|null]
     */
    public function sendOtp(string $phoneNumber): array
    {
        Log::info('=== OtpService::sendOtp called ===', [
            'provider' => $this->smsService->getProviderName(),
            'phone' => $phoneNumber,
            'timestamp' => now()->toDateTimeString()
        ]);

        return $this->smsService->sendOtp($phoneNumber);
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
        return $this->smsService->verifyOtp($phoneNumber, $otpCode);
    }

    /**
     * Check if OTP exists for phone number (not expired)
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public function hasOtp(string $phoneNumber): bool
    {
        return $this->smsService->hasOtp($phoneNumber);
    }

    /**
     * Send a generic SMS message (not OTP)
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        Log::info('=== OtpService::sendSms called ===', [
            'provider' => $this->smsService->getProviderName(),
            'phone' => $phoneNumber,
            'message_length' => strlen($message),
            'timestamp' => now()->toDateTimeString()
        ]);

        return $this->smsService->sendSms($phoneNumber, $message);
    }

    /**
     * Get the current SMS provider name
     * 
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->smsService->getProviderName();
    }
}
