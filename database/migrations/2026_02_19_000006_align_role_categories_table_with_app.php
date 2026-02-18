<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure role_categories has role_id as primary key (RoleCategory model expects primaryKey = 'role_id').
     * User model and others reference fk_role_id -> role_categories.role_id.
     */
    public function up(): void
    {
        if (!Schema::hasTable('role_categories')) {
            return;
        }
        if (Schema::hasColumn('role_categories', 'id') && !Schema::hasColumn('role_categories', 'role_id')) {
            DB::statement('ALTER TABLE role_categories CHANGE COLUMN id role_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
