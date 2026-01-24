<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsGatewayService implements SmsServiceInterface
{
    private $apiKey;
    private $apiUrl;
    private $deviceId;
    private $simSlot;
    private $useAllDevices;

    public function __construct()
    {
        $this->apiKey = config('services.sms_gateway.api_key');
        $this->apiUrl = config('services.sms_gateway.api_url', 'https://app.sms-gateway.app/services/send.php');
        $this->deviceId = config('services.sms_gateway.device_id', 0);
        $this->simSlot = config('services.sms_gateway.sim_slot');
        $this->useAllDevices = config('services.sms_gateway.use_all_devices', false);
    }

    /**
     * Send an SMS message via SMS Gateway
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits for PH)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        Log::info('=== SMS Gateway Service sendSms called ===', [
            'phone' => $phoneNumber,
            'message_length' => strlen($message),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::error('SMS Gateway API key not configured');
                return [
                    'success' => false,
                    'message' => 'SMS service is not configured. Please contact support.',
                    'message_id' => null
                ];
            }

            // Format phone number for SMS Gateway (+63XXXXXXXXXX format)
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);

            if (!$formattedNumber) {
                Log::warning('Invalid phone number format for SMS Gateway', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
                    'message_id' => null
                ];
            }

            // Prepare request parameters for SMS Gateway API
            $requestData = [
                'key' => $this->apiKey,
                'number' => $formattedNumber,
                'message' => $message,
                'type' => 'sms',
            ];

            // Device selection: use all devices if configured, otherwise use specific device
            if ($this->useAllDevices) {
                $requestData['option'] = 1;
            } else {
                if ($this->simSlot !== null && $this->deviceId > 0) {
                    $requestData['devices'] = "{$this->deviceId}|{$this->simSlot}";
                } elseif ($this->simSlot !== null && $this->deviceId == 0) {
                    $requestData['devices'] = "0|{$this->simSlot}";
                } else {
                    $requestData['devices'] = $this->deviceId;
                }
            }

            $deviceConfig = $this->useAllDevices
                ? 'all devices (auto-select)'
                : ($this->simSlot !== null
                    ? "device {$this->deviceId}, SIM slot {$this->simSlot}"
                    : "device {$this->deviceId} (default SIM)");

            Log::info('Sending SMS via SMS Gateway', [
                'phone' => $phoneNumber,
                'formatted' => $formattedNumber,
                'message_length' => strlen($message),
                'device_config' => $deviceConfig,
                'url' => $this->apiUrl
            ]);

            try {
                $response = Http::timeout(30)
                    ->asForm()
                    ->post($this->apiUrl, $requestData);
                Log::info('SMS Gateway HTTP request completed', ['status' => $response->status()]);
            } catch (\Exception $httpException) {
                Log::error('SMS Gateway HTTP request exception', [
                    'message' => $httpException->getMessage(),
                    'trace' => $httpException->getTraceAsString()
                ]);
                throw $httpException;
            }

            // Log raw response for debugging
            Log::info('SMS Gateway raw response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check if response indicates an error
                if (is_array($data) && isset($data['success']) && $data['success'] === false) {
                    $errorMessage = $data['error']['message'] ?? $data['message'] ?? 'Unknown error from SMS Gateway';

                    // Check for error code pattern
                    if (preg_match('/unknown_error\[(\d+)\]/i', $errorMessage, $matches)) {
                        $errorCode = $matches[1];
                        if ($errorCode == '124') {
                            $errorMessage = "SMS Gateway error [124]: Device may be offline or not responding. Please ensure your SMS Gateway app is running and connected.";
                        }
                    }

                    Log::error('SMS Gateway API returned error', [
                        'phone_number' => $phoneNumber,
                        'error' => $errorMessage,
                    ]);

                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'message_id' => null
                    ];
                }

                // Check if message was sent successfully
                $messageData = null;
                if (is_array($data) && isset($data['success']) && $data['success'] === true) {
                    if (isset($data['data']['data']['messages'][0])) {
                        $messageData = $data['data']['data']['messages'][0];
                    } elseif (isset($data['data']['messages'][0])) {
                        $messageData = $data['data']['messages'][0];
                    } elseif (isset($data['data']['message'])) {
                        $messageData = $data['data']['message'] ?? $data['data'];
                    }
                } elseif (is_array($data) && isset($data['data']['messages'][0])) {
                    $messageData = $data['data']['messages'][0];
                }

                if ($messageData) {
                    $messageId = $messageData['ID'] ?? $messageData['id'] ?? null;
                    $messageStatus = $messageData['status'] ?? null;
                    $errorCode = $messageData['errorCode'] ?? $messageData['resultCode'] ?? null;

                    // Check if there's an error code
                    if ($errorCode !== null) {
                        Log::error('SMS Gateway message has error code', [
                            'phone_number' => $phoneNumber,
                            'message_id' => $messageId,
                            'error_code' => $errorCode,
                        ]);

                        $errorMessage = "SMS Gateway error: unknown_error[{$errorCode}]. ";
                        if ($errorCode == 124) {
                            $errorMessage .= "Device is offline or not responding. Please ensure your SMS Gateway app is running and connected.";
                        }

                        return [
                            'success' => false,
                            'message' => $errorMessage,
                            'message_id' => null
                        ];
                    }

                    // Check if status indicates failure
                    $failedStatuses = ['Failed', 'Error', 'Rejected', 'Invalid'];
                    if (in_array($messageStatus, $failedStatuses)) {
                        Log::error('SMS Gateway message failed', [
                            'phone_number' => $phoneNumber,
                            'status' => $messageStatus,
                        ]);

                        return [
                            'success' => false,
                            'message' => "SMS delivery failed with status: {$messageStatus}.",
                            'message_id' => $messageId
                        ];
                    }

                    Log::info('SMS Gateway SMS sent successfully', [
                        'phone_number' => $phoneNumber,
                        'message_id' => $messageId,
                        'status' => $messageStatus,
                    ]);

                    // Build status message
                    $statusMessage = 'SMS sent successfully.';
                    if ($messageStatus === 'Pending' || $messageStatus === 'Queued') {
                        $statusMessage = 'SMS has been queued for delivery.';
                    }

                    return [
                        'success' => true,
                        'message' => $statusMessage,
                        'message_id' => $messageId,
                        'status' => $messageStatus
                    ];
                }

                // Unexpected response format
                Log::error('SMS Gateway unexpected response format', [
                    'phone_number' => $phoneNumber,
                    'response' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => 'Unexpected response from SMS service. Please try again.',
                    'message_id' => null
                ];
            } else {
                // Non-successful HTTP response
                $errorBody = $response->body();
                $errorStatus = $response->status();

                Log::error('SMS Gateway API error', [
                    'status' => $errorStatus,
                    'body' => $errorBody,
                    'phone_number' => $phoneNumber,
                ]);

                $errorMessage = 'Failed to send SMS. Please try again later.';
                if ($errorStatus === 401 || $errorStatus === 403) {
                    $errorMessage = 'SMS service authentication failed. Please contact support.';
                }

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'message_id' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS Gateway service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending SMS: ' . $e->getMessage(),
                'message_id' => null
            ];
        }
    }

    /**
     * Format phone number for SMS Gateway API
     * Converts to international format with + prefix: +63XXXXXXXXXX
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    public function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D+/', '', $phoneNumber);

        // Handle different input formats - convert to +63XXXXXXXXXX format
        if (strlen($digits) === 11 && $digits[0] === '0') {
            // Input: 0XXXXXXXXXX (11 digits with leading 0)
            // Remove leading 0 and add +63: +63XXXXXXXXXX
            return '+63' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && $digits[0] === '9') {
            // Input: 9XXXXXXXXX (10 digits starting with 9)
            // Add +63: +639XXXXXXXXX
            return '+63' . $digits;
        } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            // Already in format: 63XXXXXXXXXX
            // Add + prefix
            return '+' . $digits;
        } else {
            // Invalid length (must be 10 or 11 digits)
            return null;
        }
    }

    /**
     * Get the provider name
     * 
     * @return string
     */
    public function getProviderName(): string
    {
        return 'SMS Gateway';
    }

    /**
     * Send OTP via SMS Gateway
     * Generates OTP locally and sends via regular SMS
     * 
     * @param string $phoneNumber
     * @param string $message Message template (must include {otp} placeholder)
     * @return array ['success' => bool, 'message' => string, 'code' => string|null]
     */
    public function sendOtp(string $phoneNumber, string $message = null): array
    {
        Log::info('=== SMS Gateway OTP Service called ===', [
            'phone' => $phoneNumber,
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            if (empty($this->apiKey)) {
                Log::error('SMS Gateway API key not configured');
                return [
                    'success' => false,
                    'message' => 'SMS service is not configured. Please contact support.',
                    'code' => null
                ];
            }

            $formattedNumber = $this->formatPhoneNumber($phoneNumber);

            if (!$formattedNumber) {
                Log::warning('Invalid phone number format for SMS Gateway OTP', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
                    'code' => null
                ];
            }

            // Generate 6-digit OTP
            $otpCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Build message
            $otpMessage = $message ?? "Your One Time Password for iKonek176B registration is: {$otpCode}. Please use it within 5 minutes.";
            $otpMessage = str_replace('{otp}', $otpCode, $otpMessage);

            // Send via regular SMS endpoint
            $result = $this->sendSms($phoneNumber, $otpMessage);

            if ($result['success']) {
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
                'message' => $result['message'],
                'code' => null
            ];
        } catch (\Exception $e) {
            Log::error('SMS Gateway OTP service exception', [
                'message' => $e->getMessage(),
                'phone_number' => $phoneNumber
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending OTP: ' . $e->getMessage(),
                'code' => null
            ];
        }
    }
}



