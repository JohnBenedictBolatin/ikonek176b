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
        if (Schema::hasTable('users')) {
            return;
        }
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable(); // references role_categories
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('suffix', 50)->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Other'])->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('secondary_number', 20)->nullable();
            $table->string('password', 255);
            $table->string('home_address', 255)->nullable();
            $table->enum("phase", ['1', '2', '3', '4', '5',  '6'])->nullable(); // Sample values added
            $table->enum("package", ['1', '2'])->nullable(); // Add your own
            $table->string('proof_of_residency', 255)->nullable();
            $table->text('profile_description')->nullable();
            $table->timestamp('date_joined')->nullable();
            // $table->timestamps();

            // $table->foreign('role_id')->references('id')->on('role_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
