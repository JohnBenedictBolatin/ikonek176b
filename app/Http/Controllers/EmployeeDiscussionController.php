<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\TagsModel;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EmployeeDiscussionController extends Controller
{
    public function index()
    {
        Log::info('🔵 DiscussionController@index called');

        try {
            $authUser = Auth::user();
            Log::info('Auth User:', [
                'id' => $authUser->user_id ?? 'null',
                'name' => $authUser->name ?? 'null'
            ]);

            // Fetch posts with tags + author
            $posts = Post::with(['author', 'tags'])
                ->where('section', 'Discussion')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Posts fetched:', [
                'count' => $posts->count(),
                'post_ids' => $posts->pluck('post_id')->toArray(),
                'author_ids' => $posts->pluck('fk_post_author_id')->toArray(),
                'authors' => $posts->map(function($p) {
                    return [
                        'post_id' => $p->post_id,
                        'author_id' => $p->fk_post_author_id,
                        'author_name' => $p->author ? $p->author->name : 'NULL',
                        'author_role_id' => $p->author ? $p->author->fk_role_id : 'NULL'
                    ];
                })->toArray()
            ]);

            $formattedPosts = $posts->map(function ($post) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                
                if ($post->author) {
                    $authorName = $post->author->name ?? 'Unknown User';
                    // Use profile_pic if available, otherwise fallback to avatar
                    if ($post->author->profile_pic) {
                        $authorAvatar = $post->author->profile_pic;
                        if (!str_starts_with($authorAvatar, 'http') && !str_starts_with($authorAvatar, '/storage/')) {
                            $authorAvatar = '/storage/' . $authorAvatar;
                        }
                    } else {
                        $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';
                    }
                }

                $roleMap = [
                    1 => 'Resident',
                    2 => 'Barangay Captain',
                    3 => 'Barangay Secretary',
                    4 => 'Barangay Treasurer',
                    5 => 'Barangay Kagawad',
                    6 => 'SK Chairman',
                    7 => 'Sangguniang Kabataan Kagawad',
                    9 => 'System Admin',
                ];

                $authorRole = 'Resident';
                if ($post->author) {
                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';
                }

                $createdAt = $post->created_at ?? now();

                $title = strlen($post->content) > 50
                    ? substr($post->content, 0, 50) . '...'
                    : ($post->content ?: 'Untitled Post');

                $images = [];
                if (!empty($post->image_content)) {
                    // image_content stores the path in storage (e.g., posts/xxx.jpg)
                    $images[] = asset('storage/' . $post->image_content);
                }

                // Get reaction counts
                $likes = DB::table('post_reactions')
                    ->where('fk_post_id', $post->post_id)
                    ->where('reaction_type', 'Like')
                    ->count();
                
                $dislikes = DB::table('post_reactions')
                    ->where('fk_post_id', $post->post_id)
                    ->where('reaction_type', 'Dislike')
                    ->count();

                // Get comment count
                $comments = DB::table('post_comments')
                    ->where('fk_post_id', $post->post_id)
                    ->count();

                // Check if current user has reacted
                $userLiked = false;
                $userDisliked = false;
                if ($authUser) {
                    $userId = $authUser->user_id ?? $authUser->id;
                    $userReaction = DB::table('post_reactions')
                        ->where('fk_post_id', $post->post_id)
                        ->where('fk_reactor_id', $userId)
                        ->first();
                    
                    if ($userReaction) {
                        $userLiked = $userReaction->reaction_type === 'Like';
                        $userDisliked = $userReaction->reaction_type === 'Dislike';
                    }
                }

                // Safely get header - handle case where column might not exist
                $postHeader = null;
                if (Schema::hasColumn('posts', 'header')) {
                    try {
                        $headerValue = $post->header ?? null;
                        // Convert empty string to null for display
                        $postHeader = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
                    } catch (\Exception $e) {
                        // Column doesn't exist or error accessing, use null
                        $postHeader = null;
                    }
                }

                return [
                    'id' => $post->post_id,
                    'author' => $authorName,
                    'avatar' => $authorAvatar,
                    'role' => $authorRole,
                    'tags' => $post->tags->pluck('tag_name')->toArray(),
                    'date' => Carbon::parse($createdAt)->toISOString(),
                    'time' => Carbon::parse($createdAt)->format('g:i A'),
                    'title' => $title,
                    'header' => $postHeader,
                    'content' => $post->content,
                    'images' => $images,
                    'video_content' => $post->video_content,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'comments' => $comments,
                    'userLiked' => $userLiked,
                    'userDisliked' => $userDisliked,
                    'commentsList' => [],
                ];
            });

            return Inertia::render('User/Employee/E_Discussion', [
                'posts' => $formattedPosts->values()->toArray(),
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? 'Resident',
                        'fk_role_id' => $authUser->fk_role_id ?? 1,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR in index: ' . $e->getMessage());
            return Inertia::render('User/Employee/E_Discussion', [
                'posts' => [],
                'auth' => ['user' => $authUser ?? null],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        Log::info('🔵 DiscussionController@create called');

        try {
            $tags = TagsModel::orderBy('tag_name', 'asc')->get();

            Log::info('Tags fetched:', ['count' => $tags->count()]);

            return Inertia::render('User/Employee/E_Discussion_AddPost', [
                'availableTags' => $tags->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());

            return Inertia::render('User/Employee/E_Discussion_AddPost', [
                'availableTags' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        Log::info('🔵 DiscussionController@store called', $request->all());

        $validated = $request->validate([
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['required', 'array', 'min:1'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'image' => ['nullable', 'file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'],
        ]);

        try {
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->back()->with('error', 'You must be logged in to post.');
            }

            // Check if user is restricted from posting
            $restriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
            if ($restriction && $restriction->restrict_posting) {
                return redirect()->back()->with('error', 'You are restricted from creating posts. Please check your notifications for more information.');
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

            // Image upload -> store path in image_content
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('posts', 'public');
                $post->image_content = $path;
            }

            $post->video_content = $request->input('video_content', null);
            $post->is_poll = $request->boolean('is_poll') ? 1 : 0;
            $post->is_reported = 0;

            $post->save();

            // Attach tags
            if (!empty($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);

                Log::info('✅ Post created with tags', [
                    'post_id' => $post->post_id,
                    'tag_ids' => $validated['tag_ids']
                ]);
            }

            return redirect()->route('discussion_employee')
                ->with('success', 'Post published successfully!');

        } catch (\Exception $e) {
            Log::error('❌ Exception in store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }
}
