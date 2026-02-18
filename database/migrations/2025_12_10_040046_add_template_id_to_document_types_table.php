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
        if (!Schema::hasTable('document_types') || Schema::hasColumn('document_types', 'template_id')) {
            return;
        }
        Schema::table('document_types', function (Blueprint $table) {
            $table->string('template_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('document_types') || !Schema::hasColumn('document_types', 'template_id')) {
            return;
        }
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('template_id');
        });
    }
};
