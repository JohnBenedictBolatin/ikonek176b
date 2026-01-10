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

class AnnouncementController extends Controller
{
    public function index()
    {
        Log::info('🔵 AnnouncementController@index called');

        try {
            $authUser = Auth::user();
            
            // Ensure user is authenticated
            if (!$authUser) {
                Log::warning('❌ No authenticated user, redirecting to login');
                return redirect()->route('login')->with('error', 'Please login to access announcements.');
            }
            
            // Log user role for debugging
            Log::info('Auth User Details:', [
                'id' => $authUser->user_id ?? 'null',
                'name' => $authUser->name ?? 'null',
                'role_id' => $authUser->fk_role_id ?? 'null',
                'role_name' => $authUser->role ?? 'null'
            ]);
            
            // Redirect residents to their own announcement page
            if ($authUser->fk_role_id == 1) {
                Log::info('🔄 User is a resident (role_id=1), redirecting to resident announcements');
                return redirect()->route('announcement_resident');
            }
            
            // Ensure user is an employee (role_id 2, 3, 4, 5, 6, 7, or 9)
            if (!in_array($authUser->fk_role_id, [2, 3, 4, 5, 6, 7, 9])) {
                Log::warning('❌ User role_id is not an employee role:', ['role_id' => $authUser->fk_role_id]);
                return redirect()->route('login')->with('error', 'Access denied. Employee access required.');
            }
            
            Log::info('✅ User is an employee, proceeding with employee announcements');

            // Employees see all announcements (approved and pending)
            $posts = Post::with(['author', 'tags'])
                ->where('section', 'Announcement')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Announcements fetched for employees:', ['count' => $posts->count()]);

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

            $formattedPosts = $posts->map(function ($post) use ($roleMap, $authUser) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                $authorRole = 'Resident';

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
                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Employee';
                }

                $createdAt = $post->created_at ? Carbon::parse($post->created_at) : now();

                $title = $post->content ? substr($post->content, 0, 50) : 'Untitled Announcement';
                if (strlen($post->content ?? '') > 50) {
                    $title .= '...';
                }

                $images = [];
                if (!empty($post->image_content)) {
                    $images[] = asset('storage/' . $post->image_content);
                }

                $tags = $post->tags && $post->tags->count() ? $post->tags->pluck('tag_name')->toArray() : ['General'];

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

                return [
                    'id' => $post->post_id,
                    'author' => $authorName,
                    'avatar' => $authorAvatar,
                    'role' => $authorRole,
                    'section' => $post->section,
                    'status' => 'Approved', // All announcements are considered approved
                    'tags' => $tags,
                    'date' => $createdAt->toISOString(),
                    'time' => $createdAt->format('g:i A'),
                    'title' => $title,
                    'header' => (function() use ($post) {
                        if (!Schema::hasColumn('posts', 'header')) {
                            return null;
                        }
                        try {
                            $headerValue = $post->header ?? null;
                            // Convert empty string to null for display
                            return (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
                        } catch (\Exception $e) {
                            return null;
                        }
                    })(),
                    'content' => $post->content ?? '',
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

            return Inertia::render('User/Employee/E_Announcement', [
                'posts' => $formattedPosts->values()->toArray(),
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? 'Employee',
                        'fk_role_id' => $authUser->fk_role_id ?? 2,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR in AnnouncementController@index: ' . $e->getMessage());
            return Inertia::render('User/Employee/E_Announcement', [
                'posts' => [],
                'auth' => ['user' => $authUser ?? null],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        Log::info('🔵 AnnouncementController@create called');

        try {
            $tags = TagsModel::orderBy('tag_name', 'asc')->get();

            Log::info('Tags fetched:', ['count' => $tags->count()]);

            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => $tags->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());

            return Inertia::render('User/Employee/E_Announcement_AddPost', [
                'availableTags' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        Log::info('🔵 AnnouncementController@store called', $request->all());

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
            // Note: status column doesn't exist in posts table, so we don't set it

            // Image upload -> store path in image_content
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('posts', 'public');
                $post->image_content = $path;
            }

            $post->video_content = $request->input('video_content', null);
            $post->is_poll = $request->boolean('is_poll') ? 1 : 0;
            $post->is_reported = 0;

            $post->save();
            
            Log::info('✅ Post saved successfully', [
                'post_id' => $post->post_id,
                'section' => $post->section,
                'author_id' => $post->fk_post_author_id
            ]);

            // Attach tags
            if (!empty($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);

                Log::info('✅ Announcement created with tags', [
                    'post_id' => $post->post_id,
                    'tag_ids' => $validated['tag_ids']
                ]);
            }

            return redirect()->route('announcement_employee')
                ->with('success', 'Announcement published successfully!');

        } catch (\Exception $e) {
            Log::error('❌ Exception in AnnouncementController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        Log::info("🔵 AnnouncementController@show called for id: {$id}");

        try {
            $authUser = Auth::user();

            $posts = Post::with(['author', 'tags'])
                ->where('section', 'Announcement')
                ->orderBy('created_at', 'desc')
                ->get();

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

            $formattedPosts = $posts->map(function ($post) use ($roleMap) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                $authorRole = 'Resident';

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
                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Employee';
                }

                $createdAt = $post->created_at ? Carbon::parse($post->created_at) : now();

                $title = $post->content ? substr($post->content, 0, 50) : 'Untitled Post';
                if (strlen($post->content ?? '') > 50) {
                    $title .= '...';
                }

                $images = [];
                if (!empty($post->image_content)) {
                    $images[] = asset('storage/' . $post->image_content);
                }

                $tags = $post->tags && $post->tags->count() ? $post->tags->pluck('tag_name')->toArray() : ['General'];

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
                    'section' => $post->section,
                    'status' => 'Approved', // All announcements are considered approved
                    'tags' => $tags,
                    'date' => $createdAt->toISOString(),
                    'time' => $createdAt->format('g:i A'),
                    'title' => $title,
                    'header' => $postHeader,
                    'content' => $post->content ?? '',
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

            $selectedFormatted = $formattedPosts->firstWhere('id', (int) $id);

            if (!$selectedFormatted) {
                Log::warning("Announcement post not found or not approved/announcement for id: {$id}");
                abort(404, 'Announcement not found.');
            }

            return Inertia::render('User/Employee/E_Announcement', [
                'posts' => $formattedPosts->values()->toArray(),
                'selectedPost' => $selectedFormatted,
                'auth' => [
                    'user' => $authUser ? [
                        'user_id' => $authUser->user_id,
                        'name' => $authUser->name,
                        'avatar' => $authUser->avatar ?? '/assets/DEFAULT.jpg',
                        'profile_pic' => $authUser->profile_pic ?? null,
                        'role' => $authUser->role ?? 'Employee',
                        'fk_role_id' => $authUser->fk_role_id ?? 2,
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('❌ ERROR in AnnouncementController@show: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return Inertia::render('User/Employee/E_Announcement', [
                'posts' => [],
                'selectedPost' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}
