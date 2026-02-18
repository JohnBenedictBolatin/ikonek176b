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
        if (Schema::hasTable('comment_reactions')) {
            return;
        }
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('reaction_type', ['Like', 'Dislike']);
            $table->timestamp('created_at')->useCurrent();
            
            // Support both schema variations
            $table->unique(['comment_id', 'user_id'], 'comment_user_reaction_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};

