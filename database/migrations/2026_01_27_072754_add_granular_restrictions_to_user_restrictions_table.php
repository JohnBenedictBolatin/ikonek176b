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
        Schema::table('user_restrictions', function (Blueprint $table) {
            // JSON columns to store arrays of document type IDs
            $table->json('restricted_document_types')->nullable()->after('restrict_document_request');
            $table->json('allowed_document_types')->nullable()->after('restricted_document_types');
            
            // JSON columns to store arrays of event assistance type names
            $table->json('restricted_event_types')->nullable()->after('restrict_event_assistance_request');
            $table->json('allowed_event_types')->nullable()->after('restricted_event_types');
            
            // Store the reason/offenses that caused the restrictions
            $table->text('restriction_reason')->nullable()->after('allowed_event_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_restrictions', function (Blueprint $table) {
            $table->dropColumn([
                'restricted_document_types',
                'allowed_document_types',
                'restricted_event_types',
                'allowed_event_types',
                'restriction_reason'
            ]);
        });
    }
};
