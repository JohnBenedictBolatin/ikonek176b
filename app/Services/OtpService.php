<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
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
     * Generate and send OTP to phone number
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits)
     * @return array ['success' => bool, 'message' => string, 'code' => int|null]
     */
    public function sendOtp(string $phoneNumber): array
    {
        // Log entry point
        Log::info('=== OTP Service sendOtp called ===', [
            'phone' => $phoneNumber,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::error('SMS Gateway API key not configured');
                return [
                    'success' => false,
                    'message' => 'SMS service is not configured. Please contact support.',
                    'code' => null
                ];
            }
            
            Log::info('API key is configured', ['api_key_length' => strlen($this->apiKey)]);

            // Format phone number for SMS Gateway (needs international format with +: +63XXXXXXXXXX)
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            if (!$formattedNumber) {
                Log::warning('Invalid phone number format', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
                    'code' => null
                ];
            }

            // Check rate limiting (max 10 OTP requests per hour per number)
            $rateLimitKey = "otp_rate_limit:{$phoneNumber}";
            $attempts = Cache::get($rateLimitKey, 0);
            
            if ($attempts >= 10) {
                Log::warning('Rate limit exceeded', [
                    'phone' => $phoneNumber,
                    'attempts' => $attempts
                ]);
                return [
                    'success' => false,
                    'message' => 'Too many OTP requests. Please try again in an hour.',
                    'code' => null
                ];
            }

            // Generate 6-digit OTP code
            $otpCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Prepare message with OTP
            $message = "Your One Time Password for iKonek176B registration is: {$otpCode}. Please use it within 5 minutes.";

            // Log the request details (without exposing API key)
            $deviceConfig = $this->useAllDevices 
                ? 'all devices (auto-select)' 
                : ($this->simSlot !== null 
                    ? "device {$this->deviceId}, SIM slot {$this->simSlot}" 
                    : "device {$this->deviceId} (default SIM)");
            
            Log::info('Sending OTP request to SMS Gateway', [
                'phone' => $phoneNumber,
                'formatted' => $formattedNumber,
                'url' => $this->apiUrl,
                'message_length' => strlen($message),
                'api_key_set' => !empty($this->apiKey),
                'device_config' => $deviceConfig,
                'use_all_devices' => $this->useAllDevices,
                'device_id' => $this->deviceId,
                'sim_slot' => $this->simSlot
            ]);

            // Prepare request parameters for SMS Gateway API
            // For OTP, we prioritize the message
            $requestData = [
                'key' => $this->apiKey,
                'number' => $formattedNumber,
                'message' => $message,
                'type' => 'sms',
                'prioritize' => 1, // Prioritize OTP messages
            ];
            
            // Device selection: use all devices if configured, otherwise use specific device
            if ($this->useAllDevices) {
                // Use all available devices (option = 1 = USE_ALL_DEVICES)
                $requestData['option'] = 1;
            } else {
                // Use specific device (0 = primary, or specific device ID like 10834)
                // If SIM slot is specified, format as "deviceID|simSlot" (e.g., "10834|0" for first SIM, "10834|1" for second SIM)
                if ($this->simSlot !== null && $this->deviceId > 0) {
                    // Format: "deviceID|simSlot" - e.g., "10834|0" for device 10834, SIM slot 0
                    $requestData['devices'] = "{$this->deviceId}|{$this->simSlot}";
                } elseif ($this->simSlot !== null && $this->deviceId == 0) {
                    // If using primary device (0) with specific SIM slot, format as "0|simSlot"
                    $requestData['devices'] = "0|{$this->simSlot}";
                } else {
                    // Use device without specifying SIM slot (uses default SIM)
                    $requestData['devices'] = $this->deviceId;
                }
            }
            
            try {
                // SMS Gateway API uses POST requests with form data
                $response = Http::timeout(30)
                    ->asForm()
                    ->post($this->apiUrl, $requestData);
                Log::info('HTTP request completed', ['status' => $response->status()]);
            } catch (\Exception $httpException) {
                Log::error('HTTP request exception', [
                    'message' => $httpException->getMessage(),
                    'trace' => $httpException->getTraceAsString()
                ]);
                throw $httpException;
            }
            
            // Log raw response for debugging
            Log::info('SMS Gateway raw response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if response indicates an error (even with 200 status)
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
                        'response' => $data
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'code' => null
                    ];
                }
                
                // Log the response for debugging
                Log::info('SMS Gateway API response', [
                    'status' => $response->status(),
                    'data' => $data,
                    'data_type' => gettype($data),
                    'phone' => $phoneNumber
                ]);
                
                // Check if message was sent successfully
                // SMS Gateway returns: {"success": true, "data": {"messages": [{"ID": "...", "status": "Pending", ...}]}}
                $messageId = null;
                $messageStatus = null;
                $isSuccess = false;
                
                // Handle nested response structure: {"success": true, "data": {"messages": [...]}}
                $messageData = null;
                if (is_array($data) && isset($data['success']) && $data['success'] === true) {
                    // Check for messages array in data.data.messages (nested structure from logs)
                    if (isset($data['data']['data']['messages'][0])) {
                        $messageData = $data['data']['data']['messages'][0];
                    } elseif (isset($data['data']['messages'][0])) {
                        // Direct structure: data.messages
                        $messageData = $data['data']['messages'][0];
                    } elseif (isset($data['data']['message'])) {
                        // Single message response (from sendSingleMessage)
                        $messageData = $data['data']['message'] ?? $data['data'];
                    }
                } elseif (is_array($data) && isset($data['data']['messages'][0])) {
                    // Alternative response structure (no success wrapper)
                    $messageData = $data['data']['messages'][0];
                }
                
                if ($messageData) {
                    $messageId = $messageData['ID'] ?? $messageData['id'] ?? null;
                    $messageStatus = $messageData['status'] ?? null;
                    $errorCode = $messageData['errorCode'] ?? $messageData['resultCode'] ?? null;
                    
                    // Check if there's an error code (like 124)
                    if ($errorCode !== null) {
                        Log::error('SMS Gateway message has error code', [
                            'phone_number' => $phoneNumber,
                            'message_id' => $messageId,
                            'error_code' => $errorCode,
                            'status' => $messageStatus,
                            'message_data' => $messageData
                        ]);
                        
                        $errorMessage = "SMS Gateway error: unknown_error[{$errorCode}]. ";
                        if ($errorCode == 124) {
                            $errorMessage .= "This usually means the device is offline or not responding. Please ensure your SMS Gateway app is running and connected.";
                        }
                        
                        return [
                            'success' => false,
                            'message' => $errorMessage,
                            'code' => null
                        ];
                    }
                    
                    // Check if status indicates failure
                    $failedStatuses = ['Failed', 'Error', 'Rejected', 'Invalid'];
                    if (in_array($messageStatus, $failedStatuses)) {
                        Log::error('SMS Gateway message failed', [
                            'phone_number' => $phoneNumber,
                            'message_id' => $messageId,
                            'status' => $messageStatus,
                            'error_code' => $errorCode,
                            'message_data' => $messageData
                        ]);
                        
                        return [
                            'success' => false,
                            'message' => "SMS delivery failed with status: {$messageStatus}. Please check your SMS Gateway app and device connection.",
                            'code' => null
                        ];
                    }
                    
                    $isSuccess = true;
                }
                
                if ($isSuccess) {
                    // Store OTP in cache with 5 minute expiration
                    $cacheKey = "otp:{$phoneNumber}";
                    Cache::put($cacheKey, $otpCode, now()->addMinutes(5));
                    
                    // Increment rate limit counter (5 requests per hour)
                    Cache::put($rateLimitKey, $attempts + 1, now()->addHour());
                    
                    // Log successful OTP send (without exposing the code in logs)
                    Log::info('OTP sent successfully via SMS Gateway', [
                        'phone_number' => $phoneNumber,
                        'formatted_number' => $formattedNumber,
                        'message_id' => $messageId,
                        'message_status' => $messageStatus,
                        'full_response' => $data
                    ]);
                    
                    // Check if message status indicates it might not be sent yet
                    $statusMessage = 'OTP sent successfully to your phone number.';
                    $deviceInfo = '';
                    
                    if ($this->useAllDevices) {
                        $deviceInfo = ' (using all available devices)';
                    } elseif ($this->simSlot !== null) {
                        $deviceInfo = " (device {$this->deviceId}, SIM slot {$this->simSlot})";
                    } else {
                        $deviceInfo = " (device {$this->deviceId})";
                    }
                    
                    if ($messageStatus === 'Pending' || $messageStatus === 'Queued') {
                        $statusMessage = 'OTP has been queued but may not be sent yet.';
                        $statusMessage .= ' Please check:';
                        $statusMessage .= ' 1) SMS Gateway app is running on your Android device';
                        $statusMessage .= ' 2) Device is connected to internet';
                        $statusMessage .= ' 3) SIM card has load/credit';
                        $statusMessage .= ' 4) SMS permissions are granted';
                        $statusMessage .= $deviceInfo;
                        
                        // Log warning for queued messages
                        Log::warning('OTP message is queued - may not be sent', [
                            'phone_number' => $phoneNumber,
                            'message_id' => $messageId,
                            'status' => $messageStatus,
                            'device_config' => $deviceInfo,
                            'device_id' => $this->deviceId,
                            'sim_slot' => $this->simSlot,
                            'use_all_devices' => $this->useAllDevices
                        ]);
                    }
                    
                    return [
                        'success' => true,
                        'message' => $statusMessage,
                        'code' => $otpCode, // Only for testing, remove in production
                        'status' => $messageStatus,
                        'message_id' => $messageId
                    ];
                } else {
                    // Response indicates failure - check for various error formats
                    Log::error('SMS Gateway delivery failed', [
                        'phone_number' => $phoneNumber,
                        'formatted_number' => $formattedNumber,
                        'response' => $data,
                        'response_body' => $response->body()
                    ]);
                    
                    $errorMessage = 'Failed to send OTP. Please try again later.';
                    
                    // Check for error in different response formats
                    if (isset($data['error']['message'])) {
                        $errorMessage = $data['error']['message'];
                    } elseif (isset($data['error'])) {
                        $errorMessage = is_array($data['error']) ? ($data['error']['message'] ?? 'Unknown error') : $data['error'];
                    } elseif (isset($data['message'])) {
                        $errorMessage = $data['message'];
                    } elseif (isset($data['data']['error'])) {
                        $errorMessage = is_array($data['data']['error']) ? ($data['data']['error']['message'] ?? 'Unknown error') : $data['data']['error'];
                    }
                    
                    // Check if error message contains error code pattern like "unknown_error[124]"
                    if (preg_match('/unknown_error\[(\d+)\]/i', $errorMessage, $matches)) {
                        $errorCode = $matches[1];
                        Log::error('SMS Gateway error code detected', [
                            'error_code' => $errorCode,
                            'phone_number' => $phoneNumber
                        ]);
                        
                        if ($errorCode == '124') {
                            $errorMessage = "SMS Gateway error [124]: Device may be offline or not responding. Please ensure your SMS Gateway app is running, connected to the internet, and has SMS permissions enabled.";
                        } else {
                            $errorMessage = "SMS Gateway error [{$errorCode}]: {$errorMessage}";
                        }
                    }
                    
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'code' => null
                    ];
                }
            } else {
                // Log API error with full details
                $errorBody = $response->body();
                $errorStatus = $response->status();
                
                Log::error('SMS Gateway API error', [
                    'status' => $errorStatus,
                    'body' => $errorBody,
                    'phone_number' => $phoneNumber,
                    'formatted_number' => $formattedNumber,
                    'response_headers' => $response->headers()
                ]);
                
                // Provide more specific error message
                $errorMessage = 'Failed to send OTP. Please try again later.';
                if ($errorStatus === 401 || $errorStatus === 403) {
                    $errorMessage = 'SMS service authentication failed. Please contact support.';
                } elseif ($errorStatus === 422) {
                    $errorMessage = 'Invalid phone number format. Please check and try again.';
                }
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'code' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('OTP service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage() . '. Please try again later.',
                'code' => null
            ];
        }
    }

    /**
     * Verify OTP code for phone number
     * 
     * @param string $phoneNumber Phone number (10 digits, without +63)
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
     * Format phone number for SMS Gateway API
     * Converts to international format with + prefix: +63XXXXXXXXXX (+63 + 10 digits)
     * Based on API docs, they accept format like +11234567890
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D+/', '', $phoneNumber);
        
        // Handle different input formats - convert to +63XXXXXXXXXX format (+63 + 10 digits)
        if (strlen($digits) === 11 && $digits[0] === '0') {
            // Input: 0XXXXXXXXXX (11 digits with leading 0)
            // Remove leading 0 and add +63: +63XXXXXXXXXX
            $digits = substr($digits, 1); // Remove the 0
            return '+63' . $digits;
        } elseif (strlen($digits) === 10) {
            // Input: XXXXXXXXXX (10 digits)
            // Add +63: +63XXXXXXXXXX
            return '+63' . $digits;
        } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            // Already in international format without +: 63XXXXXXXXXX
            // Add + prefix
            return '+' . $digits;
        } elseif (strlen($digits) === 13 && substr($digits, 0, 3) === '639') {
            // Already in international format with +: +63XXXXXXXXXX
            return '+' . $digits;
        } else {
            // Invalid length (must be 10 or 11 digits)
            return null;
        }
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
     * Send a generic SMS message (not OTP)
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        Log::info('=== SMS Service sendSms called ===', [
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
                ];
            }
            
            // Format phone number for SMS Gateway
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            if (!$formattedNumber) {
                Log::warning('Invalid phone number format', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
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
            
            Log::info('Sending SMS request to SMS Gateway', [
                'phone' => $phoneNumber,
                'formatted' => $formattedNumber,
                'message_length' => strlen($message),
            ]);

            try {
                $response = Http::timeout(30)
                    ->asForm()
                    ->post($this->apiUrl, $requestData);
            } catch (\Exception $httpException) {
                Log::error('HTTP request exception', [
                    'message' => $httpException->getMessage(),
                    'trace' => $httpException->getTraceAsString()
                ]);
                throw $httpException;
            }
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Check if response indicates an error
                if (is_array($data) && isset($data['success']) && $data['success'] === false) {
                    $errorMessage = $data['error']['message'] ?? $data['message'] ?? 'Unknown error from SMS Gateway';
                    Log::error('SMS Gateway API returned error', [
                        'phone_number' => $phoneNumber,
                        'error' => $errorMessage,
                    ]);
                    return [
                        'success' => false,
                        'message' => $errorMessage,
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
                    
                    // Check if status indicates failure
                    $failedStatuses = ['Failed', 'Error', 'Rejected', 'Invalid'];
                    if (in_array($messageStatus, $failedStatuses)) {
                        Log::error('SMS delivery failed', [
                            'phone_number' => $phoneNumber,
                            'status' => $messageStatus,
                        ]);
                        return [
                            'success' => false,
                            'message' => "SMS delivery failed with status: {$messageStatus}.",
                        ];
                    }
                    
                    Log::info('SMS sent successfully', [
                        'phone_number' => $phoneNumber,
                        'message_id' => $messageId,
                        'status' => $messageStatus,
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'SMS sent successfully.',
                        'message_id' => $messageId,
                        'status' => $messageStatus,
                    ];
                } else {
                    Log::error('SMS Gateway delivery failed - unexpected response', [
                        'phone_number' => $phoneNumber,
                        'response' => $data,
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Failed to send SMS. Please try again later.',
                    ];
                }
            } else {
                Log::error('SMS Gateway API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'phone_number' => $phoneNumber,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send SMS. Please try again later.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber,
            ]);
            
            return [
                'success' => false,
                'message' => 'An error occurred while sending SMS: ' . $e->getMessage(),
            ];
        }
    }
}

