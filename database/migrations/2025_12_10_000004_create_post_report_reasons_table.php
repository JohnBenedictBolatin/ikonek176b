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
        Schema::create('post_report_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_report_id');
            $table->enum('reason', ['spam', 'harassment', 'hate', 'violence', 'inappropriate', 'false', 'other']);
            $table->timestamps();
            
            // Add foreign key constraint separately
            $table->foreign('fk_report_id')->references('report_id')->on('post_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_report_reasons');
    }
};




















