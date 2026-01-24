<?php

namespace App\Services;

interface SmsServiceInterface
{
    /**
     * Send an SMS message
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits for PH)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function sendSms(string $phoneNumber, string $message): array;

    /**
     * Format phone number for the specific provider
     * 
     * @param string $phoneNumber
     * @return string|null Formatted phone number or null if invalid
     */
    public function formatPhoneNumber(string $phoneNumber): ?string;

    /**
     * Get the provider name
     * 
     * @return string
     */
    public function getProviderName(): string;
}



