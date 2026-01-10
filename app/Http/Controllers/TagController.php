<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagsModel;
use Illuminate\Support\Facades\Log;

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
}
