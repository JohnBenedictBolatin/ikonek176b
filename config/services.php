<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'semaphore' => [
        'api_key' => env('SEMAPHORE_API_KEY'),
        'sender_name' => env('SEMAPHORE_SENDER_NAME', 'Semaphore'), // Default to 'Semaphore' if not set
    ],

    'sms_gateway' => [
        'api_key' => env('SMS_GATEWAY_API_KEY'),
        'api_url' => env('SMS_GATEWAY_API_URL', 'https://app.sms-gateway.app/services/send.php'),
        'device_id' => env('SMS_GATEWAY_DEVICE_ID', 0), // 0 = primary device, or specific device ID like 10834
        'sim_slot' => env('SMS_GATEWAY_SIM_SLOT', null), // null = default SIM, 0 = first SIM slot, 1 = second SIM slot
        'use_all_devices' => env('SMS_GATEWAY_USE_ALL_DEVICES', false), // Set to true to use all available devices
    ],

    'docugenerate' => [
        'api_key' => env('DOCUGENERATE_API_KEY'),
        'api_url' => env('DOCUGENERATE_API_URL', 'https://api.docugenerate.com/v1'),
        'region' => env('DOCUGENERATE_REGION', 'us'), // us, eu, uk, au
        'delimiters' => [
            'left' => env('DOCUGENERATE_LEFT_DELIMITER', '{'),
            'right' => env('DOCUGENERATE_RIGHT_DELIMITER', '}'),
        ],
    ],

];
