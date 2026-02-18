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
        if (Schema::hasTable('document_type_restrictions')) {
            return;
        }
        Schema::create('document_type_restrictions', function (Blueprint $table) {
            $table->id('restriction_id');
            $table->unsignedInteger('fk_document_type_id');
            $table->enum('required_offense_severity', ['Low', 'Medium', 'High'])->nullable();
            // null = no restriction, Medium/High = requires no offenses of that level
            $table->timestamps();
            
            $table->foreign('fk_document_type_id')
                  ->references('document_type_id')
                  ->on('document_types')
                  ->onDelete('cascade');
            
            $table->unique('fk_document_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_type_restrictions');
    }
};
