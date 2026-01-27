<?php

/**
 * MySQL Configuration Checker
 * Run this script to check and fix MySQL settings for large file uploads
 * Usage: php check_mysql_settings.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MySQL Configuration Checker ===\n\n";

try {
    // Check current max_allowed_packet setting
    $result = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
    if ($result) {
        $currentValue = $result->Value;
        $currentValueMB = round($currentValue / 1024 / 1024, 2);
        echo "Current max_allowed_packet: {$currentValue} bytes ({$currentValueMB} MB)\n";
        
        if ($currentValue < 67108864) { // Less than 64MB
            echo "⚠️  WARNING: max_allowed_packet is less than 64MB. This may cause 'MySQL server has gone away' errors.\n";
            echo "   Recommended: 64MB or higher\n\n";
            
            // Try to set it for the current session
            try {
                DB::statement('SET SESSION max_allowed_packet = 67108864');
                echo "✅ Set SESSION max_allowed_packet to 64MB (this only affects current session)\n";
                echo "   To make this permanent, you need to set it in MySQL configuration file.\n\n";
            } catch (\Exception $e) {
                echo "❌ Could not set SESSION max_allowed_packet: " . $e->getMessage() . "\n\n";
            }
        } else {
            echo "✅ max_allowed_packet is sufficient (64MB or higher)\n\n";
        }
    } else {
        echo "❌ Could not retrieve max_allowed_packet setting\n\n";
    }
    
    // Check wait_timeout
    $timeoutResult = DB::selectOne("SHOW VARIABLES LIKE 'wait_timeout'");
    if ($timeoutResult) {
        $timeoutValue = $timeoutResult->Value;
        echo "Current wait_timeout: {$timeoutValue} seconds\n";
        if ($timeoutValue < 600) {
            echo "⚠️  WARNING: wait_timeout is less than 600 seconds. This may cause connection timeouts.\n";
            try {
                DB::statement('SET SESSION wait_timeout = 600');
                echo "✅ Set SESSION wait_timeout to 600 seconds\n\n";
            } catch (\Exception $e) {
                echo "❌ Could not set SESSION wait_timeout: " . $e->getMessage() . "\n\n";
            }
        } else {
            echo "✅ wait_timeout is sufficient\n\n";
        }
    }
    
    // Check interactive_timeout
    $interactiveResult = DB::selectOne("SHOW VARIABLES LIKE 'interactive_timeout'");
    if ($interactiveResult) {
        $interactiveValue = $interactiveResult->Value;
        echo "Current interactive_timeout: {$interactiveValue} seconds\n";
        if ($interactiveValue < 600) {
            echo "⚠️  WARNING: interactive_timeout is less than 600 seconds.\n";
            try {
                DB::statement('SET SESSION interactive_timeout = 600');
                echo "✅ Set SESSION interactive_timeout to 600 seconds\n\n";
            } catch (\Exception $e) {
                echo "❌ Could not set SESSION interactive_timeout: " . $e->getMessage() . "\n\n";
            }
        } else {
            echo "✅ interactive_timeout is sufficient\n\n";
        }
    }
    
    // Test database connection
    echo "Testing database connection...\n";
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection successful\n\n";
    } catch (\Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    }
    
    // Check if we can set global variables (requires SUPER privilege)
    echo "Attempting to set GLOBAL max_allowed_packet (requires SUPER privilege)...\n";
    try {
        DB::statement('SET GLOBAL max_allowed_packet = 67108864');
        echo "✅ Successfully set GLOBAL max_allowed_packet to 64MB\n";
        echo "   This will persist until MySQL server restart.\n\n";
    } catch (\Exception $e) {
        echo "⚠️  Could not set GLOBAL max_allowed_packet: " . $e->getMessage() . "\n";
        echo "   This is normal if your user doesn't have SUPER privilege.\n";
        echo "   You'll need to set it in MySQL configuration file (my.cnf or my.ini) instead.\n\n";
    }
    
    echo "=== Recommendations ===\n";
    echo "1. If max_allowed_packet is less than 64MB, add this to your MySQL config file:\n";
    echo "   [mysqld]\n";
    echo "   max_allowed_packet = 64M\n";
    echo "   wait_timeout = 600\n";
    echo "   interactive_timeout = 600\n\n";
    echo "2. Then restart MySQL server for changes to take effect.\n";
    echo "3. For Herd/XAMPP/WAMP, the config file is usually in:\n";
    echo "   - Windows: C:\\ProgramData\\MySQL\\MySQL Server X.X\\my.ini\n";
    echo "   - Or check your MySQL installation directory\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}




















