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
            
            // Try to load poll data separately to avoid breaking if poll tables don't exist
            try {
                // Load polls for posts that have is_poll = 1
                $pollPostIds = $posts->where('is_poll', 1)->pluck('post_id')->toArray();
                Log::info('Poll post IDs found:', ['post_ids' => $pollPostIds, 'count' => count($pollPostIds)]);
                
                if (!empty($pollPostIds)) {
                    $polls = \App\Models\PostPoll::with(['options', 'votes'])
                        ->whereIn('post_id', $pollPostIds)
                        ->get()
                        ->keyBy('post_id');
                    
                    Log::info('Polls loaded from database:', [
                        'polls_count' => $polls->count(),
                        'poll_ids' => $polls->pluck('id')->toArray(),
                        'polls_with_options' => $polls->map(function($p) {
                            return [
                                'poll_id' => $p->id,
                                'post_id' => $p->post_id,
                                'options_count' => $p->options ? $p->options->count() : 0
                            ];
                        })->toArray()
                    ]);
                    
                    // Attach polls to posts
                    foreach ($posts as $post) {
                        if ($post->is_poll && isset($polls[$post->post_id])) {
                            $post->setRelation('poll', $polls[$post->post_id]);
                            Log::info('Poll attached to post', [
                                'post_id' => $post->post_id,
                                'poll_id' => $polls[$post->post_id]->id,
                                'options_count' => $polls[$post->post_id]->options ? $polls[$post->post_id]->options->count() : 0
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Could not load poll relationships: ' . $e->getMessage());
                Log::error('Poll loading error trace: ' . $e->getTraceAsString());
                // Continue without poll data - posts will still display
            }

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

                    // Get poll data if this is a poll post (safely handle errors)
                    $pollData = null;
                    try {
                        if ($post->is_poll) {
                            // Reload poll with options if not already loaded
                            if (!$post->poll || !$post->poll->relationLoaded('options')) {
                                $poll = \App\Models\PostPoll::with(['options', 'votes'])
                                    ->where('post_id', $post->post_id)
                                    ->first();
                            } else {
                                $poll = $post->poll;
                            }
                            
                            if ($poll) {
                                // Use the loaded votes relationship if available, otherwise query
                                $totalVotes = $poll->votes && $poll->votes->count() > 0 
                                    ? $poll->votes->count() 
                                    : ($poll->votes()->count() ?? 0);
                                
                                $userId = $authUser ? ($authUser->user_id ?? $authUser->id) : null;
                                $userVote = null;
                                if ($userId) {
                                    $userVote = \App\Models\PollVote::where('poll_id', $poll->id)
                                        ->where('user_id', $userId)
                                        ->first();
                                }

                                // Safely get options - ensure we have options
                                // Recalculate vote counts from actual votes for accuracy
                                $voteCounts = \App\Models\PollVote::where('poll_id', $poll->id)
                                    ->selectRaw('option_id, COUNT(*) as count')
                                    ->groupBy('option_id')
                                    ->pluck('count', 'option_id')
                                    ->toArray();
                                
                                $options = [];
                                if ($poll->options) {
                                    $options = $poll->options->map(function($option) use ($totalVotes, $voteCounts) {
                                        $actualCount = $voteCounts[$option->id] ?? 0;
                                        return [
                                            'id' => $option->id,
                                            'option_text' => $option->option_text ?? '',
                                            'vote_count' => max(0, $actualCount), // Use actual count, ensure non-negative
                                            'percentage' => $totalVotes > 0 ? round((max(0, $actualCount) / $totalVotes) * 100, 1) : 0,
                                        ];
                                    })->toArray();
                                } else {
                                    // If options aren't loaded, query them directly
                                    $pollOptions = \App\Models\PollOption::where('poll_id', $poll->id)
                                        ->orderBy('id', 'asc')
                                        ->get();
                                    $options = $pollOptions->map(function($option) use ($totalVotes, $voteCounts) {
                                        $actualCount = $voteCounts[$option->id] ?? 0;
                                        return [
                                            'id' => $option->id,
                                            'option_text' => $option->option_text ?? '',
                                            'vote_count' => max(0, $actualCount), // Use actual count, ensure non-negative
                                            'percentage' => $totalVotes > 0 ? round((max(0, $actualCount) / $totalVotes) * 100, 1) : 0,
                                        ];
                                    })->toArray();
                                }

                                // Always set poll data if poll exists, even if options are empty (for debugging)
                                $pollData = [
                                    'id' => $poll->id,
                                    'total_votes' => $totalVotes,
                                    'user_voted_option_id' => $userVote ? $userVote->option_id : null,
                                    'options' => $options,
                                ];
                                
                                if (!empty($options)) {
                                    Log::info('✅ Poll data loaded for post ' . $post->post_id, [
                                        'poll_id' => $poll->id,
                                        'options_count' => count($options),
                                        'total_votes' => $totalVotes,
                                        'options' => array_map(function($opt) {
                                            return $opt['option_text'];
                                        }, $options)
                                    ]);
                                } else {
                                    Log::warning('⚠️ Poll has no options for post ' . $post->post_id, [
                                        'poll_id' => $poll->id,
                                        'post_id' => $post->post_id
                                    ]);
                                }
                            }
                        }
                    } catch (\Exception $pollError) {
                        Log::error('Error loading poll data for post ' . $post->post_id . ': ' . $pollError->getMessage());
                        Log::error('Poll error trace: ' . $pollError->getTraceAsString());
                        // Continue without poll data - don't break the post
                        $pollData = null;
                    }

                    return [
                        'id' => $post->post_id,
                        'author' => $authorName,
                        'author_id' => $post->fk_post_author_id ?? $post->author?->user_id ?? null,
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
                        'is_poll' => (bool) $post->is_poll,
                        'poll' => $pollData,
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
            
            // Get user restrictions
            $restrictions = null;
            if ($authUser) {
                $restriction = \App\Models\UserRestriction::where('user_id', $authUser->user_id)->first();
                if ($restriction) {
                    $restrictions = [
                        'restrict_posting' => $restriction->restrict_posting ?? false,
                        'restrict_commenting' => $restriction->restrict_commenting ?? false,
                        'restrict_document_request' => $restriction->restrict_document_request ?? false,
                        'restrict_event_assistance_request' => $restriction->restrict_event_assistance_request ?? false,
                    ];
                }
            }

            $viewName = $isEmployee ? 'User/Employee/E_Discussion' : 'User/Resident/R_Discussion';
            
            return Inertia::render($viewName, [
                'posts' => $formattedPosts->values()->toArray(),
                'restrictions' => $restrictions,
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

        // Base validation rules
        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['required', 'array', 'min:1'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
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

                        Log::info('✅ Poll created with post', [
                            'post_id' => $post->post_id,
                            'poll_id' => $poll->id,
                            'options_count' => count($pollOptions)
                        ]);
                    } catch (\Exception $pollError) {
                        Log::error('❌ Error creating poll for post ' . $post->post_id . ': ' . $pollError->getMessage());
                        // Don't fail the entire post creation if poll creation fails
                        // Just log the error
                    }
                } else {
                    Log::warning('Poll post created but not enough valid options', [
                        'post_id' => $post->post_id,
                        'options_count' => count($pollOptions)
                    ]);
                }
            }

            // Attach tags
            if (!empty($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);

                Log::info('✅ Post created with tags', [
                    'post_id' => $post->post_id,
                    'tag_ids' => $validated['tag_ids']
                ]);
            }

            Log::info('✅ Post created successfully in DiscussionController', [
                'post_id' => $post->post_id,
                'author_id' => $post->fk_post_author_id,
                'section' => $post->section,
                'content_length' => strlen($post->content),
                'has_header' => !empty($post->header),
                'has_image' => !empty($post->image_content),
                'is_poll' => $post->is_poll
            ]);

            // Return success response - frontend will handle redirect after showing modal
            // Using Inertia's back() to stay on the same page so modal can display
            return back()->with('success', 'Post published successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Inertia can handle them properly
            Log::warning('Validation failed in DiscussionController@store', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('❌ Exception in store: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
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

            // Get user restrictions
            $restrictions = null;
            if ($authUser) {
                $restriction = \App\Models\UserRestriction::where('user_id', $authUser->user_id)->first();
                if ($restriction) {
                    $restrictions = [
                        'restrict_posting' => $restriction->restrict_posting ?? false,
                        'restrict_commenting' => $restriction->restrict_commenting ?? false,
                        'restrict_document_request' => $restriction->restrict_document_request ?? false,
                        'restrict_event_assistance_request' => $restriction->restrict_event_assistance_request ?? false,
                    ];
                }
            }

            return Inertia::render('User/Resident/R_Announcement', [
                'posts' => $formattedPosts->values()->toArray(),
                'restrictions' => $restrictions,
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
        Log::info('🔵 DiscussionController@AnnounceStore called', $request->all());

        // Base validation rules
        $rules = [
            'header' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'tag_ids' => ['required', 'array', 'min:1'],
            'tag_ids.*' => ['integer', 'exists:tags,tag_id'],
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
