<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService implements SmsServiceInterface
{
    private $apiKey;
    private $apiUrl;
    private $otpApiUrl;
    private $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->apiUrl = 'https://api.semaphore.co/api/v4/messages';
        $this->otpApiUrl = 'https://api.semaphore.co/api/v4/otp';
        $this->senderName = config('services.semaphore.sender_name', 'Semaphore');
    }

    /**
     * Send an SMS message via Semaphore
     * 
     * @param string $phoneNumber Phone number (10 or 11 digits for PH)
     * @param string $message Message to send
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        Log::info('=== Semaphore Service sendSms called ===', [
            'phone' => $phoneNumber,
            'message_length' => strlen($message),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::error('Semaphore API key not configured - add SEMAPHORE_API_KEY to .env file');
                return [
                    'success' => false,
                    'message' => 'Semaphore API key not configured. Please add SEMAPHORE_API_KEY to your .env file.',
                    'message_id' => null
                ];
            }

            // Format phone number for Semaphore regular SMS (63XXXXXXXXXX international format)
            $formattedNumber = $this->formatPhoneNumberForSms($phoneNumber);

            if (!$formattedNumber) {
                Log::warning('Invalid phone number format for Semaphore', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
                    'message_id' => null
                ];
            }

            // Prepare request data
            $requestData = [
                'apikey' => $this->apiKey,
                'number' => $formattedNumber,
                'message' => $message,
            ];

            // Add sender name if it's not the default
            if ($this->senderName && $this->senderName !== 'Semaphore') {
                $requestData['sendername'] = $this->senderName;
            }

            Log::info('Sending SMS via Semaphore', [
                'phone' => $phoneNumber,
                'formatted' => $formattedNumber,
                'message_length' => strlen($message),
                'sender_name' => $this->senderName,
                'url' => $this->apiUrl
            ]);

            try {
                $response = Http::timeout(10)
                    ->asForm()
                    ->post($this->apiUrl, $requestData);
                Log::info('Semaphore HTTP request completed', ['status' => $response->status()]);
            } catch (\Illuminate\Http\Client\ConnectionException $connectionException) {
                Log::error('Semaphore HTTP connection timeout/error', [
                    'message' => $connectionException->getMessage(),
                    'phone_number' => $phoneNumber
                ]);
                return [
                    'success' => false,
                    'message' => 'SMS service is not responding. Please check your connection and try again.',
                    'message_id' => null
                ];
            } catch (\Exception $httpException) {
                Log::error('Semaphore HTTP request exception', [
                    'message' => $httpException->getMessage(),
                    'trace' => $httpException->getTraceAsString(),
                    'phone_number' => $phoneNumber
                ]);
                
                // Check if it's a timeout exception
                if (strpos($httpException->getMessage(), 'timeout') !== false || 
                    strpos($httpException->getMessage(), 'timed out') !== false) {
                    return [
                        'success' => false,
                        'message' => 'SMS service request timed out. Please try again.',
                        'message_id' => null
                    ];
                }
                
                throw $httpException;
            }

            // Log raw response for debugging
            Log::info('Semaphore raw response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Semaphore parsed response', [
                    'data' => $data,
                    'data_type' => gettype($data),
                    'is_array' => is_array($data),
                    'has_index_0' => is_array($data) && isset($data[0]),
                    'has_number_error' => is_array($data) && isset($data['number']),
                ]);

                // Check for validation errors (Semaphore returns errors with 200 status)
                // Example: {"number":["The number format is invalid."]}
                if (is_array($data) && isset($data['number']) && is_array($data['number'])) {
                    $errorMsg = implode(', ', $data['number']);
                    Log::error('Semaphore validation error', [
                        'phone_number' => $phoneNumber,
                        'errors' => $data['number']
                    ]);
                    return [
                        'success' => false,
                        'message' => "Phone number error: {$errorMsg}",
                        'message_id' => null
                    ];
                }

                // Check for other error formats
                if (is_array($data) && isset($data['error'])) {
                    Log::error('Semaphore API error response', [
                        'phone_number' => $phoneNumber,
                        'error' => $data['error']
                    ]);
                    return [
                        'success' => false,
                        'message' => is_string($data['error']) ? $data['error'] : 'Semaphore API error',
                        'message_id' => null
                    ];
                }

                // Semaphore response can be an array of messages or a single message object
                $messageData = null;
                if (is_array($data)) {
                    // If it's a simple indexed array, get the first element
                    if (isset($data[0])) {
                        $messageData = $data[0];
                    } elseif (isset($data['message_id'])) {
                        // It's a single message object with message_id
                        $messageData = $data;
                    }
                }

                if ($messageData) {
                    $messageId = $messageData['message_id'] ?? $messageData['id'] ?? null;
                    $status = $messageData['status'] ?? null;
                    $network = $messageData['network'] ?? 'Unknown';
                    $recipient = $messageData['recipient'] ?? $formattedNumber;

                    Log::info('Semaphore message details', [
                        'message_id' => $messageId,
                        'status' => $status,
                        'network' => $network,
                        'recipient' => $recipient,
                        'phone_number' => $phoneNumber
                    ]);

                    // Check for failed status
                    if ($status === 'Failed' || $status === 'Rejected') {
                        Log::error('Semaphore message failed', [
                            'phone_number' => $phoneNumber,
                            'message_id' => $messageId,
                            'status' => $status,
                            'network' => $network
                        ]);

                        return [
                            'success' => false,
                            'message' => "SMS delivery failed with status: {$status}. Network detected: {$network}. Please try again.",
                            'message_id' => $messageId
                        ];
                    }

                    // Check for pending/queued status
                    $statusMessage = 'SMS sent successfully.';
                    if ($status === 'Pending' || $status === 'Queued') {
                        $statusMessage = 'SMS has been queued for delivery.';
                    } elseif ($status === 'Sent') {
                        $statusMessage = 'SMS sent successfully.';
                    }

                    Log::info('Semaphore SMS sent successfully', [
                        'phone_number' => $phoneNumber,
                        'formatted_number' => $formattedNumber,
                        'message_id' => $messageId,
                        'status' => $status,
                        'network' => $network
                    ]);

                    return [
                        'success' => true,
                        'message' => $statusMessage,
                        'message_id' => $messageId,
                        'status' => $status,
                        'network' => $network
                    ];
                }

                // Response didn't contain expected message data
                Log::error('Semaphore unexpected response format', [
                    'phone_number' => $phoneNumber,
                    'response' => $data,
                    'response_body' => $response->body()
                ]);

                // Try to extract any error message from the response
                $errorMessage = 'Unexpected response from SMS service.';
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        if (is_array($value) && !empty($value)) {
                            $errorMessage = "{$key}: " . implode(', ', $value);
                            break;
                        } elseif (is_string($value)) {
                            $errorMessage = $value;
                            break;
                        }
                    }
                }

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'message_id' => null
                ];
            } else {
                // Non-successful HTTP response
                $errorBody = $response->body();
                $errorStatus = $response->status();

                Log::error('Semaphore API error', [
                    'status' => $errorStatus,
                    'body' => $errorBody,
                    'phone_number' => $phoneNumber
                ]);

                $errorMessage = 'Failed to send SMS. Please try again later.';
                if ($errorStatus === 401 || $errorStatus === 403) {
                    $errorMessage = 'SMS service authentication failed. Please contact support.';
                } elseif ($errorStatus === 422) {
                    $errorMessage = 'Invalid request to SMS service. Please check phone number format.';
                }

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'message_id' => null
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $connectionException) {
            Log::error('Semaphore connection exception', [
                'message' => $connectionException->getMessage(),
                'phone_number' => $phoneNumber
            ]);

            return [
                'success' => false,
                'message' => 'SMS service is not responding. Please check your connection and try again.',
                'message_id' => null
            ];
        } catch (\Exception $e) {
            Log::error('Semaphore service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone_number' => $phoneNumber
            ]);

            // Check if it's a timeout exception
            $errorMessage = 'An error occurred while sending SMS. Please try again.';
            if (strpos($e->getMessage(), 'timeout') !== false || 
                strpos($e->getMessage(), 'timed out') !== false) {
                $errorMessage = 'SMS service request timed out. Please try again.';
            } elseif (strpos($e->getMessage(), 'Connection') !== false) {
                $errorMessage = 'Cannot connect to SMS service. Please check your connection and try again.';
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'message_id' => null
            ];
        }
    }

    /**
     * Format phone number for Semaphore API
     * For OTP endpoint: use local format 09XXXXXXXXX
     * For regular SMS: use 63XXXXXXXXXX format
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    public function formatPhoneNumber(string $phoneNumber): ?string
    {
        // This method is used for OTP - returns local format (09XXXXXXXXX)
        return $this->formatPhoneNumberForOtp($phoneNumber);
    }

    /**
     * Format phone number for Semaphore OTP endpoint (local format: 09XXXXXXXXX)
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    private function formatPhoneNumberForOtp(string $phoneNumber): ?string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D+/', '', $phoneNumber);

        // Handle different input formats - convert to 09XXXXXXXXX format (local)
        // Semaphore OTP endpoint requires local format!
        if (strlen($digits) === 11 && $digits[0] === '0') {
            // Input: 09XXXXXXXXX (11 digits with leading 0) - already correct
            return $digits;
        } elseif (strlen($digits) === 10 && $digits[0] === '9') {
            // Input: 9XXXXXXXXX (10 digits starting with 9)
            // Add leading 0: 09XXXXXXXXX
            return '0' . $digits;
        } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            // Input: 63XXXXXXXXXX (international without +)
            // Convert to local: 0XXXXXXXXXX
            return '0' . substr($digits, 2);
        } elseif (strlen($digits) === 13 && substr($digits, 0, 3) === '639') {
            // Input: +639XXXXXXXXX stored as digits
            // Convert to local: 09XXXXXXXXX
            return '0' . substr($digits, 2);
        } else {
            // Invalid length (must be 10 or 11 digits)
            return null;
        }
    }

    /**
     * Format phone number for Semaphore regular SMS endpoint (international format: 63XXXXXXXXXX)
     * 
     * @param string $phoneNumber
     * @return string|null
     */
    private function formatPhoneNumberForSms(string $phoneNumber): ?string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D+/', '', $phoneNumber);

        // Handle different input formats - convert to 63XXXXXXXXXX format (international)
        // Semaphore regular SMS endpoint prefers international format
        if (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            // Input: 63XXXXXXXXXX (international without +) - already correct
            return $digits;
        } elseif (strlen($digits) === 13 && substr($digits, 0, 3) === '639') {
            // Input: +639XXXXXXXXX stored as digits
            // Already in international format: 639XXXXXXXXX -> 63XXXXXXXXXX
            return '63' . substr($digits, 2);
        } elseif (strlen($digits) === 11 && $digits[0] === '0') {
            // Input: 09XXXXXXXXX (11 digits with leading 0)
            // Convert to international: 63XXXXXXXXXX
            return '63' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && $digits[0] === '9') {
            // Input: 9XXXXXXXXX (10 digits starting with 9)
            // Convert to international: 63XXXXXXXXXX
            return '63' . $digits;
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
        return 'Semaphore';
    }

    /**
     * Send OTP via Semaphore's dedicated OTP endpoint
     * Uses https://api.semaphore.co/api/v4/otp with {otp} placeholder
     * 
     * @param string $phoneNumber
     * @param string $message Message template (can include {otp} placeholder)
     * @return array ['success' => bool, 'message' => string, 'code' => string|null]
     */
    public function sendOtp(string $phoneNumber, string $message = null): array
    {
        Log::info('=== Semaphore OTP Service called ===', [
            'phone' => $phoneNumber,
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            if (empty($this->apiKey)) {
                Log::error('Semaphore API key not configured');
                return [
                    'success' => false,
                    'message' => 'Semaphore API key not configured. Please add SEMAPHORE_API_KEY to your .env file.',
                    'code' => null
                ];
            }

            // Format phone number for Semaphore OTP endpoint (local format: 09XXXXXXXXX)
            $formattedNumber = $this->formatPhoneNumberForOtp($phoneNumber);

            if (!$formattedNumber) {
                Log::warning('Invalid phone number format for Semaphore OTP', ['phone' => $phoneNumber]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter 10 or 11 digits.',
                    'code' => null
                ];
            }

            // Use the {otp} placeholder - Semaphore will auto-generate the code
            $otpMessage = $message ?? "Your One Time Password for iKonek176B registration is: {otp}. Please use it within 5 minutes.";

            // Prepare request data for OTP endpoint
            $requestData = [
                'apikey' => $this->apiKey,
                'number' => $formattedNumber,
                'message' => $otpMessage,
            ];

            // Add sender name if it's not the default
            if ($this->senderName && $this->senderName !== 'Semaphore') {
                $requestData['sendername'] = $this->senderName;
            }

            Log::info('Sending OTP via Semaphore OTP endpoint', [
                'phone' => $phoneNumber,
                'formatted' => $formattedNumber,
                'message' => $otpMessage,
                'sender_name' => $this->senderName,
                'url' => $this->otpApiUrl
            ]);

            $httpStartTime = microtime(true);
            try {
                $response = Http::timeout(10)
                    ->asForm()
                    ->post($this->otpApiUrl, $requestData);
                $httpTime = microtime(true) - $httpStartTime;
                Log::info('Semaphore OTP HTTP request completed', [
                    'status' => $response->status(),
                    'http_request_time_ms' => round($httpTime * 1000, 2)
                ]);
            } catch (\Illuminate\Http\Client\ConnectionException $connectionException) {
                $httpTime = microtime(true) - $httpStartTime;
                Log::error('Semaphore OTP HTTP connection timeout/error', [
                    'message' => $connectionException->getMessage(),
                    'phone_number' => $phoneNumber,
                    'http_request_time_ms' => round($httpTime * 1000, 2)
                ]);
                Log::error('Semaphore OTP HTTP connection timeout/error', [
                    'message' => $connectionException->getMessage(),
                    'phone_number' => $phoneNumber
                ]);
                return [
                    'success' => false,
                    'message' => 'OTP service is not responding. Please check your connection and try again.',
                    'code' => null
                ];
            } catch (\Exception $httpException) {
                Log::error('Semaphore OTP HTTP request exception', [
                    'message' => $httpException->getMessage(),
                    'trace' => $httpException->getTraceAsString(),
                    'phone_number' => $phoneNumber
                ]);
                
                // Check if it's a timeout exception
                if (strpos($httpException->getMessage(), 'timeout') !== false || 
                    strpos($httpException->getMessage(), 'timed out') !== false) {
                    return [
                        'success' => false,
                        'message' => 'OTP service request timed out. Please try again.',
                        'code' => null
                    ];
                }
                
                throw $httpException;
            }

            // Log raw response for debugging
            Log::info('Semaphore OTP raw response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Semaphore OTP parsed response', [
                    'data' => $data,
                    'data_type' => gettype($data),
                ]);

                // Check for validation errors
                if (is_array($data) && isset($data['number']) && is_array($data['number'])) {
                    $errorMsg = implode(', ', $data['number']);
                    Log::error('Semaphore OTP validation error', [
                        'phone_number' => $phoneNumber,
                        'errors' => $data['number']
                    ]);
                    return [
                        'success' => false,
                        'message' => "Phone number error: {$errorMsg}",
                        'code' => null
                    ];
                }

                // Check for other error formats
                if (is_array($data) && isset($data['error'])) {
                    Log::error('Semaphore OTP API error response', [
                        'phone_number' => $phoneNumber,
                        'error' => $data['error']
                    ]);
                    return [
                        'success' => false,
                        'message' => is_string($data['error']) ? $data['error'] : 'Semaphore API error',
                        'code' => null
                    ];
                }

                // Parse successful OTP response
                // Response format: [{"message_id": 12345, "code": 332200, "status": "Pending", ...}]
                $messageData = null;
                if (is_array($data) && isset($data[0])) {
                    $messageData = $data[0];
                } elseif (is_array($data) && isset($data['message_id'])) {
                    $messageData = $data;
                }

                if ($messageData && isset($messageData['code'])) {
                    $messageId = $messageData['message_id'] ?? null;
                    $otpCode = (string) $messageData['code'];
                    $status = $messageData['status'] ?? 'Pending';
                    $network = $messageData['network'] ?? 'Unknown';
                    $recipient = $messageData['recipient'] ?? $formattedNumber;

                    Log::info('Semaphore OTP sent successfully', [
                        'phone_number' => $phoneNumber,
                        'formatted_number' => $formattedNumber,
                        'message_id' => $messageId,
                        'status' => $status,
                        'network' => $network,
                        'recipient' => $recipient
                    ]);

                    // Check for failed status
                    if ($status === 'Failed' || $status === 'Rejected') {
                        return [
                            'success' => false,
                            'message' => "OTP delivery failed with status: {$status}. Network: {$network}.",
                            'code' => null
                        ];
                    }

                    return [
                        'success' => true,
                        'message' => 'OTP sent successfully to your phone number.',
                        'code' => $otpCode,
                        'message_id' => $messageId,
                        'status' => $status,
                        'network' => $network
                    ];
                }

                // Response didn't contain expected OTP data
                Log::error('Semaphore OTP unexpected response format', [
                    'phone_number' => $phoneNumber,
                    'response' => $data,
                    'response_body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Unexpected response from OTP service. Please try again.',
                    'code' => null
                ];
            } else {
                // Non-successful HTTP response
                $errorBody = $response->body();
                $errorStatus = $response->status();

                Log::error('Semaphore OTP API error', [
                    'status' => $errorStatus,
                    'body' => $errorBody,
                    'phone_number' => $phoneNumber
                ]);

                $errorMessage = 'Failed to send OTP. Please try again later.';
                if ($errorStatus === 401 || $errorStatus === 403) {
                    $errorMessage = 'OTP service authentication failed. Please contact support.';
                }

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'code' => null
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $connectionException) {
            Log::error('Semaphore OTP connection exception', [
                'message' => $connectionException->getMessage(),
                'phone_number' => $phoneNumber
            ]);

            return [
                'success' => false,
                'message' => 'OTP service is not responding. Please check your connection and try again.',
                'code' => null
            ];
        } catch (\Exception $e) {
            Log::error('Semaphore OTP service exception', [
                'message' => $e->getMessage(),
                'phone_number' => $phoneNumber
            ]);

            // Check if it's a timeout exception
            $errorMessage = 'An error occurred while sending OTP. Please try again.';
            if (strpos($e->getMessage(), 'timeout') !== false || 
                strpos($e->getMessage(), 'timed out') !== false) {
                $errorMessage = 'OTP service request timed out. Please try again.';
            } elseif (strpos($e->getMessage(), 'Connection') !== false) {
                $errorMessage = 'Cannot connect to OTP service. Please check your connection and try again.';
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'code' => null
            ];
        }
    }
}


