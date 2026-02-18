<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\TagsModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class PostController extends Controller
{
    // ============================
    // STORE A NEW POST
    // ============================
    public function store(Request $request)
    {
        Log::info('PostController@store called', $request->all());

        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'custom_tag_names' => ['nullable', 'array', 'max:10'],
            'custom_tag_names.*' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'],
            'is_poll' => ['nullable', 'boolean'],
        ];

        // Only require poll_options if is_poll is true
        if ($request->boolean('is_poll')) {
            $rules['poll_options'] = ['required', 'array', 'min:2', 'max:10'];
            $rules['poll_options.*'] = ['required', 'string', 'max:255'];
        } else {
            $rules['poll_options'] = ['nullable', 'array'];
            $rules['poll_options.*'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $tagIds = self::resolveTagIds($validated['tag_ids'] ?? [], $validated['custom_tag_names'] ?? []);
        if (empty($tagIds)) {
            return redirect()->back()->withInput()->withErrors(['tag_ids' => 'Please select at least one tag or add a custom tag.']);
        }

        try {
            $userId = Auth::id();
            if ($userId) {
                // Check if user is restricted from posting
                $restriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
                if ($restriction && $restriction->restrict_posting) {
                    return redirect()->back()->with('error', 'You are restricted from creating posts. Please check your notifications for more information.');
                }
            }

            $post = new Post();
            $post->fk_post_author_id = $userId;
            $post->section = 'Discussion';
            $post->content = $validated['content'];

            // Only set header if column exists in database
            if (Schema::hasColumn('posts', 'header')) {
                $headerValue = $validated['header'] ?? null;
                // Convert empty string to null
                $post->header = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
            }

            // store image file path into image_content column
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('posts', 'public');
                $post->image_content = $path;
            }

            // video stored in video_content column (string/url/path)
            $post->video_content = $request->input('video_content', null);

            $isPoll = $request->boolean('is_poll');
            $post->is_poll = $isPoll ? 1 : 0;
            $post->is_reported = 0;

            $post->save();

            // Create poll if this is a poll post
            if ($isPoll && $request->has('poll_options') && is_array($request->poll_options)) {
                $pollOptions = array_filter($request->poll_options, function($option) {
                    return !empty(trim($option));
                });

                if (count($pollOptions) >= 2) {
                    try {
                        $poll = \App\Models\PostPoll::create([
                            'post_id' => $post->post_id,
                        ]);

                        foreach ($pollOptions as $optionText) {
                            \App\Models\PollOption::create([
                                'poll_id' => $poll->id,
                                'option_text' => trim($optionText),
                                'vote_count' => 0,
                            ]);
                        }

                        Log::info('✅ Poll created with post in PostController', [
                            'post_id' => $post->post_id,
                            'poll_id' => $poll->id,
                            'options_count' => count($pollOptions),
                            'options' => $pollOptions
                        ]);
                    } catch (\Exception $pollError) {
                        Log::error('❌ Error creating poll for post ' . $post->post_id . ' in PostController: ' . $pollError->getMessage());
                        Log::error('Poll creation error trace: ' . $pollError->getTraceAsString());
                        // Don't fail the entire post creation if poll creation fails
                    }
                } else {
                    Log::warning('Poll post created but not enough valid options in PostController', [
                        'post_id' => $post->post_id,
                        'options_count' => count($pollOptions)
                    ]);
                }
            }

            // Attach tags through pivot table (resolved from tag_ids + custom_tag_names)
            $post->tags()->sync($tagIds);
            Log::info('Tags attached to post', [
                'post_id' => $post->post_id,
                'tag_ids' => $tagIds
            ]);

            Log::info('Post created successfully', ['post_id' => $post->post_id]);

            return redirect()->route('discussion_resident')
                ->with('success', 'Post published successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating post: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================
    // FETCH POSTS FOR DISCUSSION PAGE
    // ============================
    public function index()
    {
        $posts = Post::with(['author', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                // Safely get header - handle case where column might not exist
                $postHeader = null;
                if (Schema::hasColumn('posts', 'header')) {
                    try {
                        $headerValue = $p->header ?? null;
                        // Convert empty string to null for display
                        $postHeader = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
                    } catch (\Exception $e) {
                        // Column doesn't exist or error accessing, use null
                        $postHeader = null;
                    }
                }

                return [
                    'post_id' => $p->post_id,
                    'author' => [
                        'user_id' => $p->author->user_id ?? null,
                        'name' => $p->author->name ?? 'Unknown',
                        'avatar' => $p->author->avatar ?? '/assets/DEFAULT.jpg',
                    ],
                    'content' => $p->content,
                    'header' => $postHeader,
                    // return stored image path (same semantics as before — frontend should use storage URL)
                    'image_content' => $p->image_content,
                    'video_content' => $p->video_content,
                    'section' => $p->section,
                    'is_poll' => (bool) $p->is_poll,
                    'is_reported' => (bool) $p->is_reported,
                    // removed 'status' because it is not in Post.php
                    'tags' => $p->tags->pluck('tag_name')->toArray(),
                    'created_at' => $p->created_at ? $p->created_at->format('Y-m-d H:i') : null,
                ];
            });

        return Inertia::render('R_Discussion', [
            'userPost' => $posts,
        ]);
    }

    /**
     * Delete a post (only by the author)
     */
    public function destroy($postId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            $userId = $user->user_id ?? $user->id;
            $postAuthorId = $post->fk_post_author_id ?? $post->user_id ?? null;

            // Check if user is the author
            if ($userId != $postAuthorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own posts.',
                ], 403);
            }

            // Delete associated image if exists
            if ($post->image_content) {
                Storage::disk('public')->delete($post->image_content);
            }

            // Delete the post (cascade should handle related records)
            $post->delete();

            Log::info('Post deleted by author', [
                'post_id' => $postId,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update a post (only by the author)
     */
    public function update(Request $request, $postId)
    {
        Log::info('PostController@update called', ['post_id' => $postId, 'request' => $request->all()]);

        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'custom_tag_names' => ['nullable', 'array', 'max:10'],
            'custom_tag_names.*' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        $tagIds = self::resolveTagIds($validated['tag_ids'] ?? [], $validated['custom_tag_names'] ?? []);
        if (empty($tagIds)) {
            return redirect()->back()->withInput()->withErrors(['tag_ids' => 'Please select at least one tag or add a custom tag.']);
        }

        try {
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->back()->with('error', 'You must be logged in to edit posts.');
            }

            $post = Post::where('post_id', $postId)
                ->where('section', 'Discussion')
                ->first();

            if (!$post) {
                return redirect()->back()->with('error', 'Post not found.');
            }

            // Check if user is the author
            if ($post->fk_post_author_id != $userId) {
                return redirect()->back()->with('error', 'You can only edit your own posts.');
            }

            // Update post content
            $post->content = $validated['content'];

            // Only set header if column exists in database
            if (Schema::hasColumn('posts', 'header')) {
                $headerValue = $validated['header'] ?? null;
                $post->header = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
            }

            // Handle image update
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($post->image_content) {
                    Storage::disk('public')->delete($post->image_content);
                }
                // Store new image
                $image = $request->file('image');
                $path = $image->store('posts', 'public');
                $post->image_content = $path;
            }

            // Update video content
            if ($request->has('video_content')) {
                $post->video_content = $request->input('video_content', null);
            }

            $post->save();

            // Update tags (resolved from tag_ids + custom_tag_names)
            $post->tags()->sync($tagIds);
            Log::info('Tags updated for post', [
                'post_id' => $post->post_id,
                'tag_ids' => $tagIds
            ]);

            Log::info('Post updated successfully', ['post_id' => $post->post_id]);

            return redirect()->route('discussion_resident')
                ->with('success', 'Post updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating post: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating post: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Resolve tag_ids and custom_tag_names into a single array of tag IDs.
     * Custom names are trimmed and find-or-create in tags table.
     */
    protected static function resolveTagIds(array $tagIds, array $customNames): array
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($tagIds))));
        foreach (array_unique(array_filter(array_map('trim', $customNames))) as $name) {
            if ($name === '') {
                continue;
            }
            $tag = TagsModel::firstOrCreate(['tag_name' => $name]);
            $ids[] = $tag->tag_id;
        }
        return array_values(array_unique($ids));
    }

    /**
     * Check if posts exist (for polling deleted posts)
     * Accepts an array of post IDs and returns which ones still exist
     */
    public function checkPosts(Request $request)
    {
        try {
            $request->validate([
                'post_ids' => 'required|array',
                'post_ids.*' => 'integer',
            ]);

            $postIds = $request->input('post_ids', []);
            
            if (empty($postIds)) {
                return response()->json([
                    'success' => true,
                    'existing_posts' => [],
                ]);
            }

            // Get all existing post IDs
            $existingPostIds = Post::whereIn('post_id', $postIds)
                ->pluck('post_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'existing_posts' => $existingPostIds,
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking posts.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
