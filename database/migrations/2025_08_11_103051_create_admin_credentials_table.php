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
        Schema::create('admin_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->enum('role', ['Admin', 'Registration and Document Approver', 'Content Moderator']);
            $table->unsignedBigInteger('admin_id')->nullable(); // optional reference to users.admin?
            $table->timestamps();

            // if admin_id relates to users:
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_credentials');
    }
};
