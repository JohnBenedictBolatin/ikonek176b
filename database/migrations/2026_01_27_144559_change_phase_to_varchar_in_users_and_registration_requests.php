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
        if (Schema::hasTable('registration_requests') && Schema::hasColumn('registration_requests', 'phase')) {
            DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
            DB::statement("UPDATE `registration_requests` SET `phase` = CONCAT('Phase ', `phase`) WHERE `phase` IN ('2', '3', '5') AND `phase` NOT LIKE 'Phase %'");
        }
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phase')) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `phase` VARCHAR(50) NULL");
            DB::statement("UPDATE `users` SET `phase` = CONCAT('Phase ', `phase`) WHERE `phase` IN ('1', '2', '3', '4', '5', '6') AND `phase` NOT LIKE 'Phase %'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phase')) {
            DB::statement("UPDATE `users` SET `phase` = REPLACE(`phase`, 'Phase ', '') WHERE `phase` LIKE 'Phase %'");
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `phase` ENUM('1', '2', '3', '4', '5', '6') NULL");
        }
        if (Schema::hasTable('registration_requests') && Schema::hasColumn('registration_requests', 'phase')) {
            DB::statement("UPDATE `registration_requests` SET `phase` = REPLACE(`phase`, 'Phase ', '') WHERE `phase` LIKE 'Phase %'");
            DB::statement("ALTER TABLE `registration_requests` MODIFY COLUMN `phase` ENUM('2', '3', '5') NULL");
        }
    }
};
