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
        Schema::create('event_assistance_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            // Use unsignedInteger to match int(11) unsigned in event_assistance_requests table
            $table->unsignedInteger('fk_event_assist_request_id');
            $table->string('field_name', 100); // The field name from extra_fields (e.g., 'waiver_form', 'death_certificate')
            $table->string('file_name', 255);
            $table->string('file_path', 500); // Storage path
            $table->string('file_type', 50)->nullable(); // MIME type
            $table->unsignedBigInteger('file_size')->nullable(); // File size in bytes
            $table->timestamps();

            $table->index('fk_event_assist_request_id');
            // Note: Foreign key constraint removed to avoid migration issues
            // Application-level referential integrity will be maintained
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_assistance_attachments');
    }
};
