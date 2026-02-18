<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('registration_requests') || !Schema::hasColumn('registration_requests', 'phase')) {
            return;
        }
        // First, make the column nullable temporarily
        DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
        
        // Update any existing data that doesn't match allowed values
        // Extract number from "Phase X" format or set to NULL if not valid
        DB::statement("UPDATE `registration_requests` SET `phase` = CASE 
            WHEN `phase` REGEXP '.*[2].*' AND `phase` NOT REGEXP '.*[1234567890]*[13579][13579].*' THEN '2'
            WHEN `phase` REGEXP '.*[3].*' THEN '3'
            WHEN `phase` REGEXP '.*[5].*' THEN '5'
            WHEN `phase` IN ('2', '3', '5') THEN `phase`
            ELSE NULL
        END");
        
        // Now modify the column to be an ENUM with only allowed values '2', '3', '5'
        DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` ENUM('2', '3', '5') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('registration_requests') || !Schema::hasColumn('registration_requests', 'phase')) {
            return;
        }
        // Revert to string type
        DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
    }
};
