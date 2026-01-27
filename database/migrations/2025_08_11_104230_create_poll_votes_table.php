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
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id'); // users.user_id is int(11) unsigned
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('poll_id');
            $table->foreign('poll_id')->references('id')->on('post_polls')->onDelete('cascade');
            $table->unsignedBigInteger('option_id');
            $table->foreign('option_id')->references('id')->on('poll_options')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'poll_id']); // One vote per user per poll
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
