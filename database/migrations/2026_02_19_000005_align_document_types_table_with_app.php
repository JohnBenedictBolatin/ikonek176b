<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure document_types has document_type_id as primary key (DocumentType model expects primaryKey = 'document_type_id').
     * document_type_restrictions.fk_document_type_id may reference this table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('document_types')) {
            return;
        }
        if (Schema::hasColumn('document_types', 'id') && !Schema::hasColumn('document_types', 'document_type_id')) {
            if (Schema::hasTable('document_type_restrictions') && Schema::hasColumn('document_type_restrictions', 'fk_document_type_id')) {
                try {
                    Schema::table('document_type_restrictions', function (Blueprint $table) {
                        $table->dropForeign(['fk_document_type_id']);
                    });
                } catch (\Throwable $e) {
                    // FK may not exist
                }
            }
            DB::statement('ALTER TABLE document_types MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE document_types DROP PRIMARY KEY');
            DB::statement('ALTER TABLE document_types CHANGE COLUMN id document_type_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            if (Schema::hasTable('document_type_restrictions') && Schema::hasColumn('document_type_restrictions', 'fk_document_type_id')) {
                try {
                    Schema::table('document_type_restrictions', function (Blueprint $table) {
                        $table->foreign('fk_document_type_id')->references('document_type_id')->on('document_types')->onDelete('cascade');
                    });
                } catch (\Throwable $e) {
                    // May already exist
                }
            }
        }
    }

    public function down(): void
    {
        // Optional: not reverting to avoid breaking FKs
    }
};
