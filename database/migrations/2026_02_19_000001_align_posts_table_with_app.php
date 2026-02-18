<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure posts table has all columns the app expects (post_id, fk_post_author_id, section, header, image_content, video_content, is_reported).
     */
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        // Rename id to post_id if needed (model and FKs expect post_id)
        if (Schema::hasColumn('posts', 'id') && !Schema::hasColumn('posts', 'post_id')) {
            if (Schema::hasTable('post_comments') && Schema::hasColumn('post_comments', 'post_id')) {
                try {
                    Schema::table('post_comments', function (Blueprint $table) {
                        $table->dropForeign(['post_id']);
                    });
                } catch (\Throwable $e) {
                    // FK may not exist or already reference post_id
                }
            }
            DB::statement('ALTER TABLE posts CHANGE COLUMN id post_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            if (Schema::hasTable('post_comments') && Schema::hasColumn('post_comments', 'post_id')) {
                try {
                    Schema::table('post_comments', function (Blueprint $table) {
                        $table->foreign('post_id')->references('post_id')->on('posts')->onDelete('cascade');
                    });
                } catch (\Throwable $e) {
                    // May already exist
                }
            }
        }

        // Add fk_post_author_id if missing (app uses this; migration may have created user_id)
        if (!Schema::hasColumn('posts', 'fk_post_author_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('fk_post_author_id')->nullable();
            });
            if (Schema::hasColumn('posts', 'user_id')) {
                DB::table('posts')->update(['fk_post_author_id' => DB::raw('user_id')]);
            }
        }

        // section: added in 2026_02_19_000000_add_section_to_posts_table
        if (!Schema::hasColumn('posts', 'section')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('section', 50)->nullable();
            });
            if (Schema::hasColumn('posts', 'category')) {
                DB::table('posts')->whereNotNull('category')->update(['section' => DB::raw('category')]);
            }
        }

        // header: added in add_post_header migration
        if (!Schema::hasColumn('posts', 'header')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('header', 255)->nullable();
            });
        }

        // image_content (app uses this; migration may have created image_path)
        if (!Schema::hasColumn('posts', 'image_content')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->text('image_content')->nullable();
            });
            if (Schema::hasColumn('posts', 'image_path')) {
                DB::table('posts')->whereNotNull('image_path')->update(['image_content' => DB::raw('image_path')]);
            }
        }

        // video_content (app uses this; migration may have created video_path)
        if (!Schema::hasColumn('posts', 'video_content')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('video_content', 500)->nullable();
            });
            if (Schema::hasColumn('posts', 'video_path')) {
                DB::table('posts')->whereNotNull('video_path')->update(['video_content' => DB::raw('video_path')]);
            }
        }

        // is_reported
        if (!Schema::hasColumn('posts', 'is_reported')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_reported')->default(false);
            });
        }
    }

    public function down(): void
    {
        // Optional: not reverting renames to avoid breaking data
    }
};
