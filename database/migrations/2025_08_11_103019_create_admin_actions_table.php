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
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('target_user_id');
            $table->enum('action', ['Suspend', 'Unsuspend']);
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // $table->foreign('admin_id')->references('id')->on('admin_credentials')->onDelete('cascade');
            // $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_actions');
    }
};
