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
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 20)->unique();
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('document_type_id')->nullable()->constrained('document_types')->onDelete('set null');
            $table->date('birthdate')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Other'])->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->unsignedBigInteger('role_id')->nullable(); // reference to role_categories if used
            $table->string('contact_number', 20)->nullable();
            $table->string('secondary_number', 20)->nullable();
            $table->string('identification_file', 255)->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('approver_id')->nullable(); // admin id or user id?
            $table->string('pickup_what', 255)->nullable();
            $table->string('pickup_where', 255)->nullable();
            $table->dateTime('pickup_when_start')->nullable();
            $table->dateTime('pickup_when_end')->nullable();
            $table->string('pickup_by', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();

            // $table->foreign('approver_id')->references('id')->on('admin_credentials')->onDelete('set null');
            // $table->foreign('role_id')->references('id')->on('role_categories')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
