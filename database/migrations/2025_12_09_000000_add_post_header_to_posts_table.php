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
        // Check if column already exists before adding
        if (!Schema::hasColumn('posts', 'header')) {
            Schema::table('posts', function (Blueprint $table) {
                // Check if fk_post_author_id exists to use after(), otherwise just add the column
                if (Schema::hasColumn('posts', 'fk_post_author_id')) {
                    $table->string('header', 255)->nullable()->after('fk_post_author_id');
                } else {
                    $table->string('header', 255)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('header');
        });
    }
};
