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
        if (Schema::hasTable('user_offenses')) {
            return;
        }
        Schema::create('user_offenses', function (Blueprint $table) {
            $table->id('user_offense_id');
            $table->unsignedInteger('fk_user_id');
            $table->unsignedBigInteger('fk_resident_offense_id')->nullable(); // Link to original resident offense
            $table->string('offense_type', 255);
            $table->enum('severity_level', ['Low', 'Medium', 'High']);
            $table->date('offense_date')->nullable();
            $table->enum('status', ['Active', 'Resolved', 'Expunged'])->default('Active');
            $table->timestamps();
            
            $table->foreign('fk_user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('fk_resident_offense_id')
                  ->references('offense_id')
                  ->on('resident_offenses')
                  ->onDelete('set null');
            
            $table->index(['fk_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_offenses');
    }
};
