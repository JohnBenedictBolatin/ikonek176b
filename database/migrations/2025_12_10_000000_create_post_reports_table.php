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
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->unsignedBigInteger('fk_post_id');
            $table->unsignedBigInteger('fk_reporter_id');
            $table->enum('reason', ['spam', 'harassment', 'hate', 'violence', 'inappropriate', 'false', 'other'])->default('other');
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'dismissed', 'resolved'])->default('pending');
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('fk_post_id')->references('post_id')->on('posts')->onDelete('cascade');
            $table->foreign('fk_reporter_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reports');
    }
};








