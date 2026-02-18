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
use Illuminate\Support\Facades\RateLimiter;
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
            
            // Redirect all users (residents and officials) to unified announcement page
            Log::info('🔄 Redirecting to unified announcement page');
            return redirect()->route('announcement_resident');

            // Employees see all announcements (approved and pending)
            $posts = Post::with(['author', 'tags', 'poll.options', 'poll.votes'])
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
                6 => 'SK Chairman',
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
                    // Check if image_content is JSON (multiple images) or single path
                    $decoded = json_decode($post->image_content, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Multiple images stored as JSON array
                        foreach ($decoded as $path) {
                            $images[] = asset('storage/' . $path);
                        }
                    } else {
                        // Single image stored as string path
                        $images[] = asset('storage/' . $post->image_content);
                    }
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

                // Get poll data if this is a poll post
                $pollData = null;
                if ($post->is_poll && $post->poll) {
                    $poll = $post->poll;
                    $totalVotes = $poll->votes()->count();
                    $userId = $authUser ? ($authUser->user_id ?? $authUser->id) : null;
                    $userVote = null;
                    if ($userId) {
                        $userVote = \App\Models\PollVote::where('poll_id', $poll->id)
                            ->where('user_id', $userId)
                            ->first();
                    }

                    $pollData = [
                        'id' => $poll->id,
                        'total_votes' => $totalVotes,
                        'user_voted_option_id' => $userVote ? $userVote->option_id : null,
                        'options' => $poll->options->map(function($option) use ($totalVotes) {
                            return [
                                'id' => $option->id,
                                'option_text' => $option->option_text,
                                'vote_count' => $option->vote_count,
                                'percentage' => $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0,
                            ];
                        })->toArray(),
                    ];
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
                    'is_poll' => (bool) $post->is_poll,
                    'poll' => $pollData,
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

            return Inertia::render('User/Resident/R_Announcement_AddPost', [
                'availableTags' => $tags->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());

            return Inertia::render('User/Resident/R_Announcement_AddPost', [
                'availableTags' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        // Rate limiting: Max 3 posts per minute per user
        $userId = Auth::id();
        $key = 'post_creation:' . $userId;
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->withInput()
                ->withErrors(['content' => "Too many posts. Please wait {$seconds} seconds before posting again."]);
        }
        
        RateLimiter::hit($key, 60); // 60 seconds = 1 minute
        
        Log::info('🔵 AnnouncementController@store called', [
            'all' => $request->all(),
            'has_images' => $request->hasFile('images'),
            'has_image' => $request->hasFile('image'),
            'files' => array_keys($request->allFiles())
        ]);

        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'custom_tag_names' => ['nullable', 'array', 'max:10'],
            'custom_tag_names.*' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'image', 'max:5120'], // Legacy single image support
            'images' => ['nullable', 'array', 'max:10'], // Multiple images support
            'images.*' => ['file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'],
            'is_poll' => ['nullable', 'boolean'],
        ];
        
        // Handle tag_ids if sent as JSON string (from FormData)
        if ($request->has('tag_ids') && is_string($request->input('tag_ids'))) {
            $request->merge(['tag_ids' => json_decode($request->input('tag_ids'), true) ?? []]);
        }
        // Handle custom_tag_names if sent as JSON string (from FormData)
        if ($request->has('custom_tag_names') && is_string($request->input('custom_tag_names'))) {
            $request->merge(['custom_tag_names' => json_decode($request->input('custom_tag_names'), true) ?? []]);
        }
        
        // Handle poll_options if sent as JSON string (from FormData)
        if ($request->has('poll_options') && is_string($request->input('poll_options'))) {
            $request->merge(['poll_options' => json_decode($request->input('poll_options'), true) ?? []]);
        }

        // Only require poll_options if is_poll is true
        if ($request->boolean('is_poll')) {
            $rules['poll_options'] = ['required', 'array', 'min:2', 'max:10'];
            $rules['poll_options.*'] = ['required', 'string', 'max:255'];
        } else {
            $rules['poll_options'] = ['nullable', 'array'];
            $rules['poll_options.*'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $tagIds = $this->resolveTagIds($validated['tag_ids'] ?? [], $validated['custom_tag_names'] ?? []);
        if (empty($tagIds)) {
            return redirect()->back()->withInput()->withErrors(['tag_ids' => 'Please select at least one tag or add a custom tag.']);
        }

        // Check for profanity in content and header
        $profanityCheck = \App\Services\ProfanityFilterService::validateContent(
            $validated['content'] ?? null,
            $validated['header'] ?? null
        );

        if (!$profanityCheck['is_valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['content' => $profanityCheck['message']]);
        }

        // Check poll options for profanity if it's a poll
        if ($request->boolean('is_poll') && $request->has('poll_options')) {
            $pollOptions = is_array($request->poll_options) 
                ? $request->poll_options 
                : json_decode($request->poll_options, true) ?? [];
            
            foreach ($pollOptions as $option) {
                if (!empty($option) && \App\Services\ProfanityFilterService::containsProfanity($option)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['poll_options' => 'Poll options contain inappropriate language. Please remove offensive words.']);
                }
            }
        }

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
            $post->section = 'Announcement';
            $post->content = $validated['content'];

            // Only set header if column exists in database
            if (Schema::hasColumn('posts', 'header')) {
                $headerValue = $validated['header'] ?? null;
                // Convert empty string to null
                $post->header = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
            }
            // Note: status column doesn't exist in posts table, so we don't set it

            // Handle multiple images - store as JSON array in image_content
            $imagePaths = [];
            
            // Get all uploaded files
            $allFiles = $request->allFiles();
            
            Log::info('All files received', [
                'keys' => array_keys($allFiles),
                'count' => count($allFiles),
                'has_images' => $request->hasFile('images'),
                'has_image' => $request->hasFile('image')
            ]);
            
            // Handle multiple images - check for 'images' key (FormData sends as 'images[]')
            // Laravel automatically converts 'images[]' to 'images' array
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                // Handle both array and single file
                if (is_array($images)) {
                    foreach ($images as $index => $image) {
                        if ($image && $image->isValid()) {
                            try {
                                $path = $image->store('posts', 'public');
                                $imagePaths[] = $path;
                                Log::info("Image $index stored", ['path' => $path]);
                            } catch (\Exception $e) {
                                Log::error('Error storing image: ' . $e->getMessage());
                            }
                        } else {
                            Log::warning("Image $index is invalid", ['image' => $image]);
                        }
                    }
                } else {
                    // Single file uploaded as images[]
                    if ($images && $images->isValid()) {
                        try {
                            $path = $images->store('posts', 'public');
                            $imagePaths[] = $path;
                            Log::info('Single image stored', ['path' => $path]);
                        } catch (\Exception $e) {
                            Log::error('Error storing image: ' . $e->getMessage());
                        }
                    }
                }
                
                Log::info('Images processed from images[]', [
                    'count' => count($imagePaths),
                    'paths' => $imagePaths,
                    'input_type' => is_array($images) ? 'array' : 'single',
                    'input_count' => is_array($images) ? count($images) : 1
                ]);
            } elseif (isset($allFiles['images'])) {
                // Fallback: check allFiles directly
                $images = $allFiles['images'];
                if (is_array($images)) {
                    foreach ($images as $index => $image) {
                        if ($image && $image->isValid()) {
                            try {
                                $path = $image->store('posts', 'public');
                                $imagePaths[] = $path;
                                Log::info("Image $index stored (fallback)", ['path' => $path]);
                            } catch (\Exception $e) {
                                Log::error('Error storing image: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            
            // Handle single image (legacy support)
            if ($request->hasFile('image') && empty($imagePaths)) {
                try {
                    $path = $request->file('image')->store('posts', 'public');
                    $imagePaths[] = $path;
                    Log::info('Single image processed', ['path' => $path]);
                } catch (\Exception $e) {
                    Log::error('Error storing single image: ' . $e->getMessage());
                }
            }
            
            // Store images as JSON array if multiple, or single path if one
            if (!empty($imagePaths)) {
                if (count($imagePaths) === 1) {
                    $post->image_content = $imagePaths[0]; // Store single path for backward compatibility
                } else {
                    $post->image_content = json_encode($imagePaths); // Store as JSON array for multiple images
                }
            }

            $post->video_content = $request->input('video_content', null);
            $isPoll = $request->boolean('is_poll');
            $post->is_poll = $isPoll ? 1 : 0;
            $post->is_reported = 0;

            // Check for spam before saving
            $spamCheck = \App\Services\AntiSpamService::checkAndHandleSpam(
                $userId,
                $validated['content'],
                $validated['header'] ?? null
            );
            
            if ($spamCheck['is_spam']) {
                return redirect()->back()
                    ->with('error', 'Your post has been removed due to spam detection. Multiple duplicate posts were detected. Your posting privileges have been restricted. Please check your notifications for more information.');
            }

            $post->save();
            
            Log::info('✅ Post saved successfully', [
                'post_id' => $post->post_id,
                'section' => $post->section,
                'author_id' => $post->fk_post_author_id
            ]);

            // Create poll if this is a poll post
            if ($isPoll && $request->has('poll_options') && is_array($request->poll_options)) {
                $pollOptions = array_filter($request->poll_options, function($option) {
                    return !empty(trim($option));
                });

                if (count($pollOptions) >= 2) {
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

                    Log::info('✅ Poll created with announcement', [
                        'post_id' => $post->post_id,
                        'poll_id' => $poll->id,
                        'options_count' => count($pollOptions)
                    ]);
                }
            }

            // Attach tags (resolved from tag_ids + custom_tag_names)
            $post->tags()->sync($tagIds);
            Log::info('✅ Announcement created with tags', [
                'post_id' => $post->post_id,
                'tag_ids' => $tagIds
            ]);

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
                6 => 'SK Chairman',
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
                    // Check if image_content is JSON (multiple images) or single path
                    $decoded = json_decode($post->image_content, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Multiple images stored as JSON array
                        foreach ($decoded as $path) {
                            $images[] = asset('storage/' . $path);
                        }
                    } else {
                        // Single image stored as string path
                        $images[] = asset('storage/' . $post->image_content);
                    }
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

    public function update(Request $request, $id)
    {
        Log::info('🔵 AnnouncementController@update called', [
            'post_id' => $id,
            'all' => $request->all(),
            'has_images' => $request->hasFile('images'),
            'has_image' => $request->hasFile('image'),
            'files' => array_keys($request->allFiles())
        ]);

        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
            'custom_tag_names' => ['nullable', 'array', 'max:10'],
            'custom_tag_names.*' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'image', 'max:5120'], // Legacy single image support
            'images' => ['nullable', 'array', 'max:10'], // Multiple images support
            'images.*' => ['file', 'image', 'max:5120'],
            'video_content' => ['nullable', 'string'],
            'is_poll' => ['nullable', 'boolean'],
        ];
        
        // Handle tag_ids if sent as JSON string (from FormData)
        if ($request->has('tag_ids') && is_string($request->input('tag_ids'))) {
            $request->merge(['tag_ids' => json_decode($request->input('tag_ids'), true) ?? []]);
        }
        if ($request->has('custom_tag_names') && is_string($request->input('custom_tag_names'))) {
            $request->merge(['custom_tag_names' => json_decode($request->input('custom_tag_names'), true) ?? []]);
        }
        
        // Handle poll_options if sent as JSON string (from FormData)
        if ($request->has('poll_options') && is_string($request->input('poll_options'))) {
            $request->merge(['poll_options' => json_decode($request->input('poll_options'), true) ?? []]);
        }

        // Only require poll_options if is_poll is true
        if ($request->boolean('is_poll')) {
            $rules['poll_options'] = ['required', 'array', 'min:2', 'max:10'];
            $rules['poll_options.*'] = ['required', 'string', 'max:255'];
        } else {
            $rules['poll_options'] = ['nullable', 'array'];
            $rules['poll_options.*'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $tagIds = $this->resolveTagIds($validated['tag_ids'] ?? [], $validated['custom_tag_names'] ?? []);
        if (empty($tagIds)) {
            return redirect()->back()->withInput()->withErrors(['tag_ids' => 'Please select at least one tag or add a custom tag.']);
        }

        try {
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->back()->with('error', 'You must be logged in to edit posts.');
            }

            $post = Post::where('post_id', $id)
                ->where('section', 'Announcement')
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

            // Handle multiple images - store as JSON array in image_content
            $imagePaths = [];
            
            // Get all uploaded files
            $allFiles = $request->allFiles();
            
            Log::info('All files received for update', [
                'keys' => array_keys($allFiles),
                'count' => count($allFiles),
                'has_images' => $request->hasFile('images'),
                'has_image' => $request->hasFile('image')
            ]);
            
            // Handle multiple images - check for 'images' key (FormData sends as 'images[]')
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                if (is_array($images)) {
                    foreach ($images as $index => $image) {
                        if ($image && $image->isValid()) {
                            try {
                                $path = $image->store('posts', 'public');
                                $imagePaths[] = $path;
                                Log::info("Image $index stored", ['path' => $path]);
                            } catch (\Exception $e) {
                                Log::error('Error storing image: ' . $e->getMessage());
                            }
                        }
                    }
                } else {
                    if ($images && $images->isValid()) {
                        try {
                            $path = $images->store('posts', 'public');
                            $imagePaths[] = $path;
                        } catch (\Exception $e) {
                            Log::error('Error storing image: ' . $e->getMessage());
                        }
                    }
                }
            } elseif (isset($allFiles['images'])) {
                $images = $allFiles['images'];
                if (is_array($images)) {
                    foreach ($images as $index => $image) {
                        if ($image && $image->isValid()) {
                            try {
                                $path = $image->store('posts', 'public');
                                $imagePaths[] = $path;
                            } catch (\Exception $e) {
                                Log::error('Error storing image: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            
            // Handle single image (legacy support)
            if ($request->hasFile('image') && empty($imagePaths)) {
                try {
                    $path = $request->file('image')->store('posts', 'public');
                    $imagePaths[] = $path;
                } catch (\Exception $e) {
                    Log::error('Error storing single image: ' . $e->getMessage());
                }
            }

            // Update images if new ones were uploaded
            if (!empty($imagePaths)) {
                if (count($imagePaths) === 1) {
                    // Delete old images
                    if ($post->image_content) {
                        $oldImages = json_decode($post->image_content, true);
                        if (is_array($oldImages)) {
                            foreach ($oldImages as $oldPath) {
                                if (Storage::disk('public')->exists($oldPath)) {
                                    Storage::disk('public')->delete($oldPath);
                                }
                            }
                        } else {
                            if (Storage::disk('public')->exists($post->image_content)) {
                                Storage::disk('public')->delete($post->image_content);
                            }
                        }
                    }
                    $post->image_content = $imagePaths[0];
                } else {
                    // Delete old images
                    if ($post->image_content) {
                        $oldImages = json_decode($post->image_content, true);
                        if (is_array($oldImages)) {
                            foreach ($oldImages as $oldPath) {
                                if (Storage::disk('public')->exists($oldPath)) {
                                    Storage::disk('public')->delete($oldPath);
                                }
                            }
                        } else {
                            if (Storage::disk('public')->exists($post->image_content)) {
                                Storage::disk('public')->delete($post->image_content);
                            }
                        }
                    }
                    $post->image_content = json_encode($imagePaths);
                }
            }

            $post->video_content = $request->input('video_content', null);
            $isPoll = $request->boolean('is_poll');
            $post->is_poll = $isPoll ? 1 : 0;

            $post->save();

            // Update tags
            $post->tags()->sync($tagIds);

            // Handle poll update
            if ($isPoll && $request->has('poll_options') && is_array($request->poll_options)) {
                $pollOptions = array_filter($request->poll_options, function($option) {
                    return !empty(trim($option));
                });

                if (count($pollOptions) >= 2) {
                    // Delete existing poll and create new one
                    $existingPoll = \App\Models\PostPoll::where('post_id', $post->post_id)->first();
                    if ($existingPoll) {
                        \App\Models\PollVote::where('poll_id', $existingPoll->id)->delete();
                        \App\Models\PollOption::where('poll_id', $existingPoll->id)->delete();
                        $existingPoll->delete();
                    }

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
                }
            } elseif (!$isPoll) {
                // Delete poll if post is no longer a poll
                $existingPoll = \App\Models\PostPoll::where('post_id', $post->post_id)->first();
                if ($existingPoll) {
                    \App\Models\PollVote::where('poll_id', $existingPoll->id)->delete();
                    \App\Models\PollOption::where('poll_id', $existingPoll->id)->delete();
                    $existingPoll->delete();
                }
            }

            Log::info('✅ Post updated successfully', [
                'post_id' => $post->post_id,
                'section' => $post->section,
                'author_id' => $post->fk_post_author_id
            ]);

            return redirect()->back()->with('success', 'Post updated successfully!');

        } catch (\Exception $e) {
            Log::error('❌ Exception in AnnouncementController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Resolve tag_ids and custom_tag_names into a single array of tag IDs.
     */
    protected function resolveTagIds(array $tagIds, array $customNames): array
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
}
