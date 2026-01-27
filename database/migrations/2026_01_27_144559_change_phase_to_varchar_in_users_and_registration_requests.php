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
        // Change registration_requests.phase from ENUM to VARCHAR
        DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
        
        // Update existing numeric values to "Phase X" format
        DB::statement("UPDATE `registration_requests` SET `phase` = CONCAT('Phase ', `phase`) WHERE `phase` IN ('2', '3', '5') AND `phase` NOT LIKE 'Phase %'");
        
        // Change users.phase from ENUM to VARCHAR
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `phase` VARCHAR(50) NULL");
        
        // Update existing numeric values to "Phase X" format
        DB::statement("UPDATE `users` SET `phase` = CONCAT('Phase ', `phase`) WHERE `phase` IN ('1', '2', '3', '4', '5', '6') AND `phase` NOT LIKE 'Phase %'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert users.phase back to ENUM
        DB::statement("UPDATE `users` SET `phase` = REPLACE(`phase`, 'Phase ', '') WHERE `phase` LIKE 'Phase %'");
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `phase` ENUM('1', '2', '3', '4', '5', '6') NULL");
        
        // Revert registration_requests.phase back to ENUM
        DB::statement("UPDATE `registration_requests` SET `phase` = REPLACE(`phase`, 'Phase ', '') WHERE `phase` LIKE 'Phase %'");
        DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` ENUM('2', '3', '5') NULL");
    }
};
