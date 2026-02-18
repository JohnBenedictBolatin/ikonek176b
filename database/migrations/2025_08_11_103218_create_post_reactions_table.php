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
        if (Schema::hasTable('post_reactions')) {
            return;
        }
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('reaction_type', ['Like', 'Dislike']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
