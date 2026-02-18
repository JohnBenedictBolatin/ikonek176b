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
        if (!Schema::hasTable('post_reports') || !Schema::hasColumn('post_reports', 'reason')) {
            return;
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE post_reports MODIFY COLUMN reason ENUM('spam', 'harassment', 'hate', 'violence', 'inappropriate', 'false', 'other') DEFAULT 'other'");
        } else {
            Schema::table('post_reports', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
            Schema::table('post_reports', function (Blueprint $table) {
                $table->enum('reason', ['spam', 'harassment', 'hate', 'violence', 'inappropriate', 'false', 'other'])->default('other');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('post_reports') || !Schema::hasColumn('post_reports', 'reason')) {
            return;
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE post_reports MODIFY COLUMN reason ENUM('spam', 'inappropriate', 'false', 'other') DEFAULT 'other'");
        } else {
            Schema::table('post_reports', function (Blueprint $table) {
                $table->dropColumn('reason');
            });
            Schema::table('post_reports', function (Blueprint $table) {
                $table->enum('reason', ['spam', 'inappropriate', 'false', 'other'])->default('other');
            });
        }
    }
};




















