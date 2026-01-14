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
            if (!Schema::hasColumn('post_reports', 'details')) {
                Schema::table('post_reports', function (Blueprint $table) {
                    $table->text('details')->nullable()->after('reason');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('post_reports') && Schema::hasColumn('post_reports', 'details')) {
            Schema::table('post_reports', function (Blueprint $table) {
                $table->dropColumn('details');
            });
        }
    }
};








