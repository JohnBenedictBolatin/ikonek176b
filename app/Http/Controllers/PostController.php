<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
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

        $validated = $request->validate([
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'], // renamed to match posts table column
        ]);

        try {
            $post = new Post();
            $post->fk_post_author_id = Auth::id();
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

            $post->is_poll = $request->boolean('is_poll') ? 1 : 0;
            $post->is_reported = 0;

            $post->save();

            // Attach tags through pivot table if provided
            if (!empty($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);
                Log::info('Tags attached to post', [
                    'post_id' => $post->post_id,
                    'tag_ids' => $validated['tag_ids']
                ]);
            }

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
}
