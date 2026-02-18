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
        if (!Schema::hasTable('registration_requests')) {
            return;
        }
        if (!Schema::hasColumn('registration_requests', 'place_of_birth')) {
            return;
        }
        Schema::table('registration_requests', function (Blueprint $table) {
            $table->string('place_of_birth')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('registration_requests')) {
            return;
        }
        if (!Schema::hasColumn('registration_requests', 'place_of_birth')) {
            return;
        }
        Schema::table('registration_requests', function (Blueprint $table) {
            $table->string('place_of_birth')->nullable(false)->change();
        });
    }
};
