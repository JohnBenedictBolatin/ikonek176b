<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking if reason_type column exists...\n";

try {
    if (Schema::hasColumn('document_requests', 'reason_type')) {
        echo "✅ Column 'reason_type' already exists.\n";
    } else {
        echo "Adding 'reason_type' column...\n";
        Schema::table('document_requests', function ($table) {
            $table->string('reason_type', 100)->nullable()->after('purpose');
        });
        echo "✅ Column 'reason_type' added successfully!\n";
    }
    
    // Verify
    $columns = DB::select("SHOW COLUMNS FROM document_requests LIKE 'reason_type'");
    if (count($columns) > 0) {
        echo "✅ Verification: Column exists in database.\n";
    } else {
        echo "❌ Warning: Column may not have been added. Check database manually.\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nYou can manually run this SQL in your database:\n";
    echo "ALTER TABLE `document_requests` ADD COLUMN `reason_type` VARCHAR(100) NULL AFTER `purpose`;\n";
}










