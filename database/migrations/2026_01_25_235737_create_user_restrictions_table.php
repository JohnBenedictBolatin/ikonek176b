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
        Schema::create('user_restrictions', function (Blueprint $table) {
            $table->id();
            // Users table uses user_id as int(11) unsigned, so we use unsignedInteger to match
            $table->unsignedInteger('user_id');
            $table->boolean('restrict_posting')->default(false);
            $table->boolean('restrict_commenting')->default(false);
            $table->boolean('restrict_document_request')->default(false);
            $table->boolean('restrict_event_assistance_request')->default(false);
            $table->unsignedInteger('restricted_by')->nullable(); // Admin who applied restriction
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Users table uses 'user_id' as primary key (int(11) unsigned)
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_restrictions');
    }
};
