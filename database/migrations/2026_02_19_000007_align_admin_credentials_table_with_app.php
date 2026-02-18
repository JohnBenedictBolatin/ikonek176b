<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure admin_credentials has admin_cred_id as primary key (AdminCredential model expects primaryKey = 'admin_cred_id').
     */
    public function up(): void
    {
        if (!Schema::hasTable('admin_credentials')) {
            return;
        }
        if (Schema::hasColumn('admin_credentials', 'id') && !Schema::hasColumn('admin_credentials', 'admin_cred_id')) {
            DB::statement('ALTER TABLE admin_credentials CHANGE COLUMN id admin_cred_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
