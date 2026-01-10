<?php

/**
 * Quick diagnostic script to check SMS Gateway setup
 * Run: php check_sms_gateway_status.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== SMS Gateway Status Check ===\n\n";

// Check 1: API Key configured
$apiKey = config('services.sms_gateway.api_key');
if (empty($apiKey)) {
    echo "❌ API Key: NOT CONFIGURED\n";
    echo "   → Add SMS_GATEWAY_API_KEY to your .env file\n\n";
} else {
    echo "✅ API Key: CONFIGURED (length: " . strlen($apiKey) . ")\n\n";
}

// Check 2: Device configuration
$useAllDevices = config('services.sms_gateway.use_all_devices', false);
$deviceId = config('services.sms_gateway.device_id', 0);
$simSlot = config('services.sms_gateway.sim_slot');

echo "📱 Device Configuration:\n";
if ($useAllDevices) {
    echo "   → Using: ALL available devices (auto-select)\n";
} else {
    echo "   → Using: Device ID {$deviceId}";
    if ($simSlot !== null) {
        echo ", SIM Slot {$simSlot}\n";
    } else {
        echo " (default SIM)\n";
    }
}
echo "\n";

// Check 3: API URL
$apiUrl = config('services.sms_gateway.api_url', 'https://app.sms-gateway.app/services/send.php');
echo "🌐 API URL: {$apiUrl}\n\n";

// Check 4: Test API connection (optional)
echo "🔍 Testing API connection...\n";
try {
    $response = \Illuminate\Support\Facades\Http::timeout(5)->get($apiUrl . '?key=' . $apiKey);
    if ($response->successful()) {
        echo "✅ API: CONNECTED\n";
    } else {
        echo "⚠️  API: Response status " . $response->status() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ API: Connection failed - " . $e->getMessage() . "\n";
}

echo "\n=== Manual Checks Required ===\n\n";
echo "Please verify on your Android device:\n\n";
echo "1. 📲 SMS Gateway App:\n";
echo "   → Open SMS Gateway app\n";
echo "   → Check if logged in\n";
echo "   → Verify app shows 'Online' or 'Connected'\n";
echo "   → Keep app running (don't close it)\n\n";

echo "2. 🌐 Internet Connection:\n";
echo "   → Device has WiFi or mobile data\n";
echo "   → Can browse internet on device\n";
echo "   → SMS Gateway app can connect to server\n\n";

echo "3. 💳 SIM Card Load:\n";
echo "   → Check balance for SIM 1: Dial *143# (Globe) or *123# (Smart/DITO)\n";
echo "   → Check balance for SIM 2: Same codes\n";
echo "   → At least ONE SIM needs to have load/credit\n\n";

echo "4. 🔐 SMS Permissions:\n";
echo "   → Settings → Apps → SMS Gateway → Permissions\n";
echo "   → Enable 'SMS' permission\n";
echo "   → Enable 'Phone' permission (if required)\n\n";

echo "5. 🔋 Battery Optimization:\n";
echo "   → Settings → Battery → Battery Optimization\n";
echo "   → Find 'SMS Gateway' → Select 'Don't optimize'\n\n";

echo "6. 📊 Web Dashboard:\n";
echo "   → Log into https://app.sms-gateway.app\n";
echo "   → Go to 'Devices' section\n";
echo "   → Verify device shows as 'Online' (not 'Offline')\n\n";

echo "=== Next Steps ===\n\n";
echo "After verifying all checks:\n";
echo "1. Clear config cache: php artisan config:clear\n";
echo "2. Test sending OTP from registration page\n";
echo "3. Check SMS Gateway dashboard for message status\n";
echo "4. Check Laravel logs: tail -f storage/logs/laravel.log | grep 'SMS Gateway'\n\n";

