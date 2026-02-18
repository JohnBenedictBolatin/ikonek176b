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
        if (!Schema::hasTable('document_requests') || !Schema::hasColumn('document_requests', 'phase')) {
            return;
        }
        DB::statement("ALTER TABLE `document_requests` MODIFY COLUMN `phase` VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('document_requests') || !Schema::hasColumn('document_requests', 'phase')) {
            return;
        }
        DB::statement("ALTER TABLE `document_requests` MODIFY COLUMN `phase` ENUM('Phase 2', 'Phase 5') NULL");
    }
};
