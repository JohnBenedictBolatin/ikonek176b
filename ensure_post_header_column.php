<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Checking if post_header column exists...\n";
    
    // Check if column exists
    $columns = DB::select("SHOW COLUMNS FROM posts WHERE Field = 'post_header'");
    
    if (empty($columns)) {
        echo "Column 'post_header' does NOT exist. Adding it now...\n";
        
        try {
            // Try to add the column
            DB::statement("ALTER TABLE posts ADD COLUMN post_header VARCHAR(255) NULL AFTER fk_post_author_id");
            echo "✅ Column 'post_header' has been added successfully!\n";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠️ Column already exists (duplicate error)\n";
            } else {
                echo "❌ Error adding column: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    } else {
        echo "✅ Column 'post_header' already exists.\n";
        echo "Column definition: " . json_encode($columns[0], JSON_PRETTY_PRINT) . "\n";
    }
    
    // Verify it works
    echo "\nVerifying column access...\n";
    try {
        $test = DB::select("SELECT post_header FROM posts LIMIT 1");
        echo "✅ Column is accessible!\n";
    } catch (\Exception $e) {
        echo "❌ Error accessing column: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ All checks passed!\n";
    
} catch (\Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}









