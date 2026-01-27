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
        // Change document_requests.phase from ENUM to VARCHAR
        DB::statement("ALTER TABLE `document_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert document_requests.phase back to ENUM
        // Note: This will only work if all values match the ENUM values
        DB::statement("ALTER TABLE `document_requests` MODIFY COLUMN `phase` ENUM('Phase 2', 'Phase 5') NULL");
    }
};
