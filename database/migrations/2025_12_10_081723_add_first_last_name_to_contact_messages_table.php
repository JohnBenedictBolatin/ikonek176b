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
        if (!Schema::hasTable('contact_messages')) {
            return;
        }
        Schema::table('contact_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_messages', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('contact_messages', 'last_name')) {
                $table->string('last_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('contact_messages')) {
            return;
        }
        $cols = [];
        if (Schema::hasColumn('contact_messages', 'first_name')) $cols[] = 'first_name';
        if (Schema::hasColumn('contact_messages', 'last_name')) $cols[] = 'last_name';
        if (!empty($cols)) {
            Schema::table('contact_messages', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }
};
