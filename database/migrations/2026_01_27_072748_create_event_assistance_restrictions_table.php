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
        if (Schema::hasTable('event_assistance_restrictions')) {
            return;
        }
        Schema::create('event_assistance_restrictions', function (Blueprint $table) {
            $table->id('restriction_id');
            $table->string('event_type', 100); // e.g., 'Court Reservation', 'Sports Equipment Borrowing'
            $table->string('offense_type', 255); // The specific offense that triggers this restriction
            $table->enum('severity_level', ['Medium', 'High']); // Only Medium and High restrict
            $table->timestamps();
            
            $table->index(['event_type', 'offense_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_assistance_restrictions');
    }
};
