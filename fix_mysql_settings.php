<?php

/**
 * Quick MySQL Settings Fix
 * This script will attempt to fix MySQL max_allowed_packet setting
 * Run: php fix_mysql_settings.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MySQL Settings Fix ===\n\n";

try {
    // Check current setting
    $result = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
    $currentValue = $result->Value ?? 0;
    $currentValueMB = round($currentValue / 1024 / 1024, 2);
    
    echo "Current max_allowed_packet: {$currentValueMB} MB\n\n";
    
    if ($currentValueMB < 64) {
        echo "Attempting to increase max_allowed_packet to 64MB...\n";
        
        // Try GLOBAL first (requires SUPER privilege, but persists)
        try {
            DB::statement('SET GLOBAL max_allowed_packet = 67108864');
            echo "✅ Successfully set GLOBAL max_allowed_packet to 64MB\n";
            echo "   This will persist until MySQL server restart.\n\n";
        } catch (\Exception $e) {
            echo "⚠️  Could not set GLOBAL (may require SUPER privilege): {$e->getMessage()}\n";
            echo "   Attempting SESSION setting instead...\n";
            
            try {
                DB::statement('SET SESSION max_allowed_packet = 67108864');
                echo "✅ Set SESSION max_allowed_packet to 64MB (session only)\n";
                echo "   ⚠️  This only affects the current connection.\n";
                echo "   For permanent fix, you need to edit MySQL config file.\n\n";
            } catch (\Exception $e2) {
                echo "❌ Could not set SESSION max_allowed_packet: {$e2->getMessage()}\n\n";
            }
        }
        
        // Verify
        $verify = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
        $verifyMB = round($verify->Value / 1024 / 1024, 2);
        echo "Verified max_allowed_packet: {$verifyMB} MB\n\n";
        
        if ($verifyMB < 16) {
            echo "❌ ERROR: max_allowed_packet is still too low!\n";
            echo "You need to manually edit MySQL configuration file.\n\n";
            echo "For Herd/MySQL on Windows:\n";
            echo "1. Find MySQL config file (usually in MySQL installation directory)\n";
            echo "2. Look for my.ini or my.cnf file\n";
            echo "3. Add or modify these lines in [mysqld] section:\n";
            echo "   [mysqld]\n";
            echo "   max_allowed_packet = 64M\n";
            echo "   wait_timeout = 600\n";
            echo "   interactive_timeout = 600\n";
            echo "4. Restart MySQL service\n\n";
        } else {
            echo "✅ MySQL settings should now work for file uploads!\n";
        }
    } else {
        echo "✅ max_allowed_packet is already sufficient ({$currentValueMB} MB)\n";
        echo "The issue may be something else. Check the Laravel logs for more details.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
















