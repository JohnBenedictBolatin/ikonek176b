<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('post_reports')) {
            // Check and add created_at if missing
            if (!Schema::hasColumn('post_reports', 'created_at')) {
                Schema::table('post_reports', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable();
                });
            }
            if (!Schema::hasColumn('post_reports', 'updated_at')) {
                Schema::table('post_reports', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('post_reports')) {
            if (Schema::hasColumn('post_reports', 'updated_at')) {
                Schema::table('post_reports', function (Blueprint $table) {
                    $table->dropColumn('updated_at');
                });
            }
            
            if (Schema::hasColumn('post_reports', 'created_at')) {
                Schema::table('post_reports', function (Blueprint $table) {
                    $table->dropColumn('created_at');
                });
            }
        }
    }
};




















