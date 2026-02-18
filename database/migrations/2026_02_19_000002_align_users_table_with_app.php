<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure users table has user_id as primary key (model expects primaryKey = 'user_id').
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }
        if (Schema::hasColumn('users', 'id') && !Schema::hasColumn('users', 'user_id')) {
            $this->dropForeignKeysReferencingUsersId();
            DB::statement('ALTER TABLE users CHANGE COLUMN id user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            $this->addForeignKeysReferencingUsersUserId();
        }
        // App uses fk_role_id; migration may have created role_id
        if (Schema::hasColumn('users', 'role_id') && !Schema::hasColumn('users', 'fk_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('fk_role_id')->nullable();
            });
            DB::table('users')->whereNotNull('role_id')->update(['fk_role_id' => DB::raw('role_id')]);
        }
    }

    private function dropForeignKeysReferencingUsersId(): void
    {
        foreach (['posts', 'post_comments', 'notifications'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->dropForeign(['user_id']);
                    });
                } catch (\Throwable $e) {
                    // FK may already be to user_id or not exist
                }
            }
        }
        if (Schema::hasTable('admin_credentials') && Schema::hasColumn('admin_credentials', 'admin_id')) {
            try {
                Schema::table('admin_credentials', function (Blueprint $t) {
                    $t->dropForeign(['admin_id']);
                });
            } catch (\Throwable $e) {
                // FK may not exist
            }
        }
    }

    private function addForeignKeysReferencingUsersUserId(): void
    {
        foreach (['posts', 'post_comments', 'notifications'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                    });
                } catch (\Throwable $e) {
                    // May already exist
                }
            }
        }
        if (Schema::hasTable('admin_credentials') && Schema::hasColumn('admin_credentials', 'admin_id')) {
            try {
                Schema::table('admin_credentials', function (Blueprint $t) {
                    $t->foreign('admin_id')->references('user_id')->on('users')->onDelete('set null');
                });
            } catch (\Throwable $e) {
                // May already exist
            }
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
