<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagsModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagController extends Controller
{
    /**
     * Return all tags (id + name) as JSON.
     * Protected by web auth middleware via route definition.
     */
    public function index(Request $request)
    {
        Log::info('🔵 TagController@index called by user_id: ' . optional($request->user())->user_id);

        // return tags ordered by name
        $tags = TagsModel::orderBy('tag_name')->get(['tag_id', 'tag_name']);

        return response()->json([
            'tags' => $tags
        ]);
    }

    /**
     * Get trending tags - tags that have been used in posts 5+ times,
     * ordered by usage count (most used first).
     */
    public function trending(Request $request)
    {
        $limit = $request->input('limit', 10); // Default to top 10 tags

        // Get trending tags that have been used in posts 5+ times
        $trendingTags = DB::table('post_tags')
            ->join('tags', 'post_tags.fk_tag_id', '=', 'tags.tag_id')
            ->join('posts', 'post_tags.fk_post_id', '=', 'posts.post_id')
            ->select(
                'tags.tag_id',
                'tags.tag_name',
                DB::raw('COUNT(DISTINCT posts.post_id) as usage_count')
            )
            ->groupBy('tags.tag_id', 'tags.tag_name')
            ->having('usage_count', '>=', 5)
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'tags' => $trendingTags->map(function ($tag) {
                return [
                    'id' => $tag->tag_id,
                    'name' => $tag->tag_name,
                    'count' => $tag->usage_count
                ];
            })
        ]);
    }
}
