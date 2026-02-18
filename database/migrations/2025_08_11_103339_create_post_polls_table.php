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
        if (Schema::hasTable('post_polls')) {
            return;
        }
        Schema::create('post_polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id'); // posts.post_id is int(11) unsigned
            $table->foreign('post_id')->references('post_id')->on('posts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_polls');
    }
};
