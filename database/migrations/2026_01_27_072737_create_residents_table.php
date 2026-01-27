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
        Schema::create('residents', function (Blueprint $table) {
            $table->id('resident_id');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 10)->nullable();
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->unsignedBigInteger('fk_role_id')->default(1); // Default to Resident (1)
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['first_name', 'last_name', 'birthdate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
