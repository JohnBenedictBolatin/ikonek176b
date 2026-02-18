<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure document_requests table has doc_request_id as primary key (model expects primaryKey = 'doc_request_id').
     */
    public function up(): void
    {
        if (!Schema::hasTable('document_requests')) {
            return;
        }
        if (Schema::hasColumn('document_requests', 'id') && !Schema::hasColumn('document_requests', 'doc_request_id')) {
            DB::statement('ALTER TABLE document_requests MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE document_requests DROP PRIMARY KEY');
            DB::statement('ALTER TABLE document_requests CHANGE COLUMN id doc_request_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
