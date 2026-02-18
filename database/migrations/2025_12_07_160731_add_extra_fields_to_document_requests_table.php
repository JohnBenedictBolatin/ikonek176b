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
        if (!Schema::hasTable('document_requests')) {
            return;
        }

        if (!Schema::hasColumn('document_requests', 'extra_fields')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->json('extra_fields')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('document_requests')) {
            return;
        }

        if (Schema::hasColumn('document_requests', 'extra_fields')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropColumn('extra_fields');
            });
        }
    }
};
