<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        if (!Schema::hasColumn('posts', 'section')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('section', 50)->nullable();
            });
            // Sync from category if it exists
            if (Schema::hasColumn('posts', 'category')) {
                DB::table('posts')->whereNotNull('category')->update(['section' => DB::raw('category')]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('posts') || !Schema::hasColumn('posts', 'section')) {
            return;
        }
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
};
