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

class DiscussionController extends Controller
{
    public function index()
    {
        Log::info('🔵 DiscussionController@index called');

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('❌ No authenticated user, redirecting to login');
            return redirect()->route('login')->with('error', 'Please sign in to access this page.');
        }

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

            Log::info('Posts fetched:', ['count' => $posts->count()]);

            $formattedPosts = $posts->map(function ($post) use ($authUser) {
                try {
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
                        6 => 'Sangguniang Kabataan Chairman',
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
                } catch (\Exception $e) {
                    Log::error('Error formatting post: ' . $e->getMessage());
                    return null; // Skip this post if there's an error
                }
            })->filter(); // Remove null values

            // Determine which view to render based on user role
            $userRoleId = $authUser->fk_role_id ?? 1;
            $isEmployee = in_array($userRoleId, [2, 3, 4, 5, 6, 7, 9]);
            
            $viewName = $isEmployee ? 'User/Employee/E_Discussion' : 'User/Resident/R_Discussion';
            
            return Inertia::render($viewName, [
                'posts' => $formattedPosts->values()->toArray(),
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? ($isEmployee ? 'Employee' : 'Resident'),
                        'fk_role_id' => $authUser->fk_role_id ?? ($isEmployee ? 2 : 1),
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR in index: ' . $e->getMessage());
            $userRoleId = $authUser->fk_role_id ?? 1;
            $isEmployee = in_array($userRoleId, [2, 3, 4, 5, 6, 7, 9]);
            $viewName = $isEmployee ? 'User/Employee/E_Discussion' : 'User/Resident/R_Discussion';
            
            return Inertia::render($viewName, [
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

            // Determine which view to render based on user role
            $authUser = Auth::user();
            $userRoleId = $authUser->fk_role_id ?? 1;
            $isEmployee = in_array($userRoleId, [2, 3, 4, 5, 6, 7, 9]);
            $viewName = $isEmployee ? 'User/Employee/E_Discussion_AddPost' : 'User/Resident/R_Discussion_AddPost';
            
            return Inertia::render($viewName, [
                'availableTags' => $tags->toArray(),
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? ($isEmployee ? 'Employee' : 'Resident'),
                        'fk_role_id' => $authUser->fk_role_id ?? ($isEmployee ? 2 : 1),
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());
            
            $authUser = Auth::user();
            $userRoleId = $authUser->fk_role_id ?? 1;
            $isEmployee = in_array($userRoleId, [2, 3, 4, 5, 6, 7, 9]);
            $viewName = $isEmployee ? 'User/Employee/E_Discussion_AddPost' : 'User/Resident/R_Discussion_AddPost';

            return Inertia::render($viewName, [
                'availableTags' => [],
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? ($isEmployee ? 'Employee' : 'Resident'),
                        'fk_role_id' => $authUser->fk_role_id ?? ($isEmployee ? 2 : 1),
                    ] : null
                ]
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

            // Redirect based on user role
            $userRoleId = Auth::user()->fk_role_id ?? 1;
            $isEmployee = in_array($userRoleId, [2, 3, 4, 5, 6, 7, 9]);
            
            return redirect()->route('discussion_resident')
                ->with('success', 'Post published successfully!');

        } catch (\Exception $e) {
            Log::error('❌ Exception in store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    

    // Announcement

    public function announcement()
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
                ->where('section', 'Announcement')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Posts fetched:', ['count' => $posts->count()]);

            $formattedPosts = $posts->map(function ($post) {
                $authorName = $post->author->name ?? 'Unknown User';
                $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';

                $roleMap = [
                    1 => 'Resident',
                    2 => 'Barangay Captain',
                    3 => 'Barangay Secretary',
                    4 => 'Barangay Treasurer',
                    5 => 'Barangay Kagawad',
                    6 => 'Sangguniang Kabataan Chairman',
                    7 => 'Sangguniang Kabataan Kagawad',
                    9 => 'System Admin',
                ];

                $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';

                $createdAt = $post->created_at ?? now();

                $title = strlen($post->content) > 50
                    ? substr($post->content, 0, 50) . '...'
                    : ($post->content ?: 'Untitled Post');

                $images = [];
                if (!empty($post->image_content)) {
                    // image_content stores the path in storage (e.g., posts/xxx.jpg)
                    $images[] = asset('storage/' . $post->image_content);
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
                    'likes' => 0,
                    'dislikes' => 0,
                    'comments' => 0,
                    'userLiked' => false,
                    'userDisliked' => false,
                    'commentsList' => [],
                    
                ];
            });

            return Inertia::render('User/Resident/R_Announcement', [
                'posts' => $formattedPosts->values()->toArray(),
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'role' => $authUser->role ?? 'Resident',
                        'fk_role_id' => $authUser->fk_role_id ?? 1,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR in index: ' . $e->getMessage());
            return Inertia::render('User/Resident/R_Announcement', [
                'posts' => [],
                'auth' => ['user' => $authUser ?? null],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function OffiCreate()
    {
        Log::info('🔵 DiscussionController@create called');

        try {
            $tags = TagsModel::orderBy('tag_name', 'asc')->get();

            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => $tags->toArray(),
                'auth' => [
                    'user' => Auth::user() ? [
                        'user_id' => Auth::user()->user_id,
                        'name' => Auth::user()->name,
                        'avatar' => Auth::user()->avatar ?? '/assets/DEFAULT.jpg',
                        'fk_role_id' => Auth::user()->fk_role_id ?? 2,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());
            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => [],
                'auth' => ['user' => Auth::user() ?? null],
            ]);
        }
    }

    public function announce()
    {
        Log::info('🔵 DiscussionController@create called');

        try {
            $tags = TagsModel::orderBy('tag_name', 'asc')->get();

            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => $tags->toArray(),
                'auth' => [
                    'user' => Auth::user() ? [
                        'user_id' => Auth::user()->user_id,
                        'name' => Auth::user()->name,
                        'avatar' => Auth::user()->avatar ?? '/assets/DEFAULT.jpg',
                        'fk_role_id' => Auth::user()->fk_role_id ?? 2,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());
            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => [],
                'auth' => ['user' => Auth::user() ?? null],
            ]);
        }
    }

    public function AnnounceStore(Request $request)
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

            $post = new Post();
            $post->fk_post_author_id = $userId;
            $post->section = 'Announcement';
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

            return redirect()->route('announcement_employee')
                ->with('success', 'Post published successfully!');

        } catch (\Exception $e) {
            Log::error('❌ Exception in store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function announcementShow($id)
    {
        Log::info('🔵 DiscussionController@announcementShow called', ['id' => $id]);

        try {
            $authUser = Auth::user();

            // fetch the single announcement post, with author/tags
            $post = Post::with(['author', 'tags'])
                ->where('post_id', $id)
                ->where('section', 'Announcement')
                ->first();

            if (!$post) {
                Log::warning('Announcement not found', ['id' => $id]);
                // fallback: redirect to announcements index (or render empty)
                return redirect()->route('announcement_employee')->with('error', 'Announcement not found.');
            }

            // format single post (same mapping as announcement())
            $roleMap = [
                1 => 'Resident',
                2 => 'Barangay Captain',
                3 => 'Barangay Secretary',
                4 => 'Barangay Treasurer',
                5 => 'Barangay Kagawad',
                6 => 'Sangguniang Kabataan Chairman',
                7 => 'Sangguniang Kabawad',
                9 => 'System Admin',
            ];

            $authorName   = $post->author->name ?? 'Unknown User';
            $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';
            $authorRole   = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';

            $createdAt = $post->created_at ?? now();
            $title = strlen($post->content) > 50
                ? substr($post->content, 0, 50) . '...'
                : ($post->content ?: 'Untitled Post');

            $images = [];
            if (!empty($post->image_content)) {
                $images[] = asset('storage/' . $post->image_content);
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

            $formattedSingle = [
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
                'likes' => 0,
                'dislikes' => 0,
                'comments' => 0,
                'userLiked' => false,
                'userDisliked' => false,
                'commentsList' => [],
            ];

            // Also fetch the announcements list (so the page has posts + selectedPost)
            $posts = Post::with(['author', 'tags'])
                ->where('section', 'Announcement')
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedPosts = $posts->map(function ($post) use ($roleMap) {
                $authorName = $post->author->name ?? 'Unknown User';
                $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';
                $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';
                $createdAt = $post->created_at ?? now();
                $title = strlen($post->content) > 50
                    ? substr($post->content, 0, 50) . '...'
                    : ($post->content ?: 'Untitled Post');
                $images = [];
                if (!empty($post->image_content)) {
                    $images[] = asset('storage/' . $post->image_content);
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
                    'likes' => 0,
                    'dislikes' => 0,
                    'comments' => 0,
                    'userLiked' => false,
                    'userDisliked' => false,
                    'commentsList' => [],
                ];
            });

            return Inertia::render('User/Employee/E_Announcement', [
                'posts' => $formattedPosts->values()->toArray(),
                'selectedPost' => $formattedSingle,
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'role' => $authUser->role ?? 'Resident',
                        'fk_role_id' => $authUser->fk_role_id ?? 1,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR in announcementShow: ' . $e->getMessage());
            return redirect()->route('announcement_employee')->with('error', 'Error fetching announcement.');
        }
    }

}
