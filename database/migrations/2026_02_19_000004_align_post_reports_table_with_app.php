<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure post_reports has post_report_id as primary key (PostReport model expects primaryKey = 'post_report_id').
     * post_report_reasons.fk_report_id references this table, so drop FK before rename and re-add after.
     */
    public function up(): void
    {
        if (!Schema::hasTable('post_reports')) {
            return;
        }
        if (!Schema::hasColumn('post_reports', 'report_id') || Schema::hasColumn('post_reports', 'post_report_id')) {
            return;
        }

        if (Schema::hasTable('post_report_reasons')) {
            Schema::table('post_report_reasons', function (Blueprint $table) {
                $table->dropForeign(['fk_report_id']);
            });
        }

        DB::statement('ALTER TABLE post_reports MODIFY COLUMN report_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE post_reports DROP PRIMARY KEY');
        DB::statement('ALTER TABLE post_reports CHANGE COLUMN report_id post_report_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');

        if (Schema::hasTable('post_report_reasons')) {
            Schema::table('post_report_reasons', function (Blueprint $table) {
                $table->foreign('fk_report_id')->references('post_report_id')->on('post_reports')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
