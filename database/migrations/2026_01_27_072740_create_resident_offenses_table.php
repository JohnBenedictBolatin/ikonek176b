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
        if (Schema::hasTable('resident_offenses')) {
            return;
        }
        Schema::create('resident_offenses', function (Blueprint $table) {
            $table->id('offense_id');
            $table->unsignedBigInteger('fk_resident_id');
            $table->string('offense_type', 255);
            $table->enum('severity_level', ['Low', 'Medium', 'High']);
            $table->date('offense_date')->nullable();
            $table->enum('status', ['Active', 'Resolved', 'Expunged'])->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('fk_resident_id')
                  ->references('resident_id')
                  ->on('residents')
                  ->onDelete('cascade');
            
            $table->index(['fk_resident_id', 'status']);
            $table->index('severity_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_offenses');
    }
};
