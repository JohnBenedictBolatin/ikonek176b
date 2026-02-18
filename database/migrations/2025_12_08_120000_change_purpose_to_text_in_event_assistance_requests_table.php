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
        // Use raw SQL to ensure the column type changes to LONGTEXT (can store up to 4GB)
        DB::statement('ALTER TABLE event_assistance_requests MODIFY COLUMN purpose LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Use raw SQL to revert to VARCHAR(255)
        DB::statement('ALTER TABLE event_assistance_requests MODIFY COLUMN purpose VARCHAR(255) NULL');
    }
};





















