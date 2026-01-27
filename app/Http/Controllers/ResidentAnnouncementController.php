<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Carbon\Carbon;

class ResidentAnnouncementController extends Controller
{
    /**
     * Display announcements for residents (VIEW ONLY — cannot add)
     */
    public function index()
    {
        Log::info('🔵 ResidentAnnouncementController@index called');
        
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

            // Residents see all announcements (no status column in database)
            $posts = Post::with(['author', 'tags'])
                ->where('section', 'Announcement')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Announcements fetched for residents:', ['count' => $posts->count()]);

            $authUser = Auth::user();
            $formattedPosts = $posts->map(function ($post) use ($authUser) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                $authorRole = 'Official';

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

                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Official';
                }

                $createdAt = $post->created_at ? Carbon::parse($post->created_at) : now();

                $title = $post->content ? substr($post->content, 0, 50) : 'Untitled Announcement';
                if (strlen($post->content ?? '') > 50) $title .= '...';

                $images = [];
                if (!empty($post->image_content)) {
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
                $authUser = Auth::user();
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

                // Get tags from the database
                $tags = $post->tags && $post->tags->count() ? $post->tags->pluck('tag_name')->toArray() : ['Announcement'];

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
                    'author_id' => $post->fk_post_author_id ?? $post->author?->user_id ?? null,
                    'avatar' => $authorAvatar,
                    'role' => $authorRole,
                    'tags' => $tags,
                    'date' => $createdAt->toISOString(),
                    'time' => $createdAt->format('g:i A'),
                    'title' => $title,
                    'header' => $postHeader,
                    'content' => $post->content ?? '',
                    'images' => $images,
                    'video_path' => $post->video_path,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'comments' => $comments,
                    'userLiked' => $userLiked,
                    'userDisliked' => $userDisliked,
                    'commentsList' => [],
                ];
            });

            Log::info('Formatted announcements for residents:', [
                'count' => $formattedPosts->count()
            ]);

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
                'canAddPost' => false, // 🔒 Residents cannot add announcements
                'restrictions' => $restrictions,
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
            Log::error('❌ ERROR in ResidentAnnouncementController@index: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return Inertia::render('User/Resident/R_Announcement', [
                'posts' => [],
                'canAddPost' => false, // still false
                'error' => $e->getMessage()
            ]);
        }
    }
}