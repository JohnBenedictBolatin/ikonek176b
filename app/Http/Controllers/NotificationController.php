<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\notifications;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Resolve column names for post_reactions table
     */
    private function resolvePostReactionsColumns(): array
    {
        $postCol = Schema::hasColumn('post_reactions', 'fk_post_id') ? 'fk_post_id' : 'post_id';
        $userCol = Schema::hasColumn('post_reactions', 'fk_reactor_id') ? 'fk_reactor_id' : 'user_id';
        $pkCol = Schema::hasColumn('post_reactions', 'post_reaction_id') ? 'post_reaction_id' : 'id';

        return [$postCol, $userCol, $pkCol];
    }

    /**
     * Resolve column names for post_comments table
     */
    private function resolvePostCommentsColumns(): array
    {
        $postCol = Schema::hasColumn('post_comments', 'fk_post_id') ? 'fk_post_id' : 'post_id';
        $userCol = Schema::hasColumn('post_comments', 'fk_commenter_id') ? 'fk_commenter_id' : 'user_id';
        $pkCol = Schema::hasColumn('post_comments', 'comment_id') ? 'comment_id' : 'id';

        return [$postCol, $userCol, $pkCol];
    }
    /**
     * Get all post-related notifications for the authenticated user
     * Filters out RequestStatus notifications
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            // User model uses user_id as primary key, not id
            // So Auth::id() returns user_id value
            $userId = Auth::id();
            
            // Fallback if Auth::id() doesn't work
            if (!$userId) {
                $userId = $user->user_id ?? $user->id ?? null;
            }

            \Log::info('Fetching notifications', [
                'user_id' => $userId,
                'auth_id' => Auth::id(),
                'user_object_id' => $user->id ?? null,
                'user_object_user_id' => $user->user_id ?? null
            ]);

            // Get notifications including Report type for restrictions, DocumentRequest, and EventAssistance
            // Actual table uses: fk_user_id, notification_type, notification_reference_id
            // notification_type enum: 'Post','Comment','DocumentRequest','EventAssistance','Report'
            $notifications = DB::table('notifications')
                ->where('fk_user_id', $userId)
                ->whereIn('notification_type', ['Post', 'Comment', 'Report', 'DocumentRequest', 'EventAssistance']) // Reactions are stored as Comment type, Report for restrictions
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Notifications found', [
                'count' => $notifications->count(),
                'user_id_used' => $userId,
                'sample_notifications' => $notifications->take(3)->toArray()
            ]);

            // Format notifications with user info and post details
            $formattedNotifications = $notifications->map(function ($notification) {
                $notificationData = $this->formatNotification($notification);
                return $notificationData;
            })->filter(); // Remove null entries

            return response()->json([
                'success' => true,
                'notifications' => $formattedNotifications->values()->all(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Format a notification with user and post details
     */
    private function formatNotification($notification)
    {
        try {
            // Get the actor (user who performed the action)
            $actor = $this->getActor($notification);
            if (!$actor) {
                return null;
            }

            // Get post details if available
            $post = null;
            if ($notification->notification_reference_id) {
                $post = $this->getPostDetails($notification);
            }

            // Format action text based on type
            $action = $this->getActionText($notification, $actor, $post);

            // Format time
            $time = $this->formatTime($notification->created_at);

            // Get avatar
            $avatar = $this->getAvatar($actor);

            return [
                'id' => $notification->notification_id,
                'user' => $actor['name'],
                'action' => $action,
                'avatar' => $avatar,
                'time' => $time,
                'timestamp' => $notification->created_at,
                'is_read' => (bool) $notification->is_read,
                'type' => $notification->notification_type,
                'reference_id' => $notification->notification_reference_id,
                'post_id' => $post ? $post['id'] : null,
                'post_title' => $post ? $post['title'] : null,
                'post_category' => $post ? $post['category'] : null, // 'Announcement' or 'Discussion'
            ];
        } catch (\Exception $e) {
            \Log::error('Error formatting notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the actor (user who performed the action) from notification
     */
    private function getActor($notification)
    {
        try {
            $refId = $notification->notification_reference_id;
            $type = $notification->notification_type;

            // For Report type (restrictions), return system admin with ADMIN.png
            if ($type === 'Report') {
                return [
                    'name' => 'System Administrator',
                    'avatar' => '/assets/ADMIN.png',
                ];
            }
            
            // For DocumentRequest and EventAssistance types, return system admin with ADMIN.png
            if ($type === 'DocumentRequest' || $type === 'EventAssistance') {
                return [
                    'name' => 'System Administrator',
                    'avatar' => '/assets/ADMIN.png',
                ];
            }

            // For Comment type, it could be a comment or a reaction (we store reactions as Comment type)
            // Try to determine by checking if the reference_id exists in post_reactions or post_comments
            if ($type === 'Comment' && $refId) {
                // First check if it's a reaction
                [$postCol, $userCol, $pkCol] = $this->resolvePostReactionsColumns();
                $reaction = DB::table('post_reactions')
                    ->where($pkCol, $refId)
                    ->first();

                if ($reaction) {
                    // Check which column exists for user ID
                    $reactorId = null;
                    if (isset($reaction->fk_reactor_id)) {
                        $reactorId = $reaction->fk_reactor_id;
                    } elseif (isset($reaction->user_id)) {
                        $reactorId = $reaction->user_id;
                    }
                    
                    if ($reactorId) {
                        return $this->getUserDetails($reactorId);
                    }
                }

                // If not a reaction, check if it's a comment
                [$commentPostCol, $commentUserCol, $commentPkCol] = $this->resolvePostCommentsColumns();
                $comment = DB::table('post_comments')
                    ->where($commentPkCol, $refId)
                    ->first();

                if ($comment) {
                    // Check which column exists for user ID
                    $commenterId = null;
                    if (isset($comment->fk_commenter_id)) {
                        $commenterId = $comment->fk_commenter_id;
                    } elseif (isset($comment->user_id)) {
                        $commenterId = $comment->user_id;
                    }
                    
                    if ($commenterId) {
                        return $this->getUserDetails($commenterId);
                    }
                }
            }

            // For Post type
            if ($type === 'Post' && $refId) {
                $post = DB::table('posts')
                    ->where('post_id', $refId)
                    ->first();

                if ($post) {
                    $authorId = $post->fk_post_author_id ?? $post->user_id ?? null;
                    if ($authorId) {
                        return $this->getUserDetails($authorId);
                    }
                }
            }

            // Fallback: try to extract from message if available
            if ($notification->message) {
                // Try to parse actor name from message
                // This is a fallback if the reference doesn't have user info
            }

            // Last resort: return a generic user
            return $this->getUserDetails(null);
        } catch (\Exception $e) {
            \Log::error('Error getting actor: ' . $e->getMessage());
            return $this->getUserDetails(null);
        }
    }

    /**
     * Get user details
     */
    private function getUserDetails($userId)
    {
        if (!$userId) {
            return [
                'name' => 'Someone',
                'avatar' => '/assets/DEFAULT.jpg',
            ];
        }

        // users table uses user_id as primary key, not id
        $user = DB::table('users')
            ->where('user_id', $userId)
            ->first();

        if (!$user) {
            return [
                'name' => 'Someone',
                'avatar' => '/assets/DEFAULT.jpg',
            ];
        }

        $name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Unknown User';
        
        // Prioritize profile_pic (uploaded by user) over avatar (legacy field)
        $avatar = $user->profile_pic ?? $user->avatar ?? null;

        // Format avatar URL - ensure avatar is a string
        if ($avatar && is_string($avatar)) {
            // If it's already a full URL or starts with /storage/, use as is
            if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/storage/')) {
                // Already properly formatted
            } elseif (str_starts_with($avatar, '/')) {
                // If it starts with / but not /storage/, assume it's an asset path
                // Keep as is
            } else {
                // If it doesn't start with /, prepend /storage/
                $avatar = '/storage/' . ltrim($avatar, '/');
            }
        } else {
            $avatar = '/assets/DEFAULT.jpg';
        }

        return [
            'name' => $name,
            'avatar' => $avatar ?: '/assets/DEFAULT.jpg',
        ];
    }

    /**
     * Get post details
     */
    private function getPostDetails($notification)
    {
        try {
            $postId = null;
            $refId = $notification->notification_reference_id;
            $type = $notification->notification_type;

            // Get post ID from reference
            if ($type === 'Post') {
                $postId = $refId;
            } elseif ($type === 'Comment') {
                // Could be a comment or reaction - check both
                // First check if it's a reaction
                [$postCol, $userCol, $pkCol] = $this->resolvePostReactionsColumns();
                $reaction = DB::table('post_reactions')
                    ->where($pkCol, $refId)
                    ->first();
                if ($reaction) {
                    $postId = $reaction->{$postCol} ?? null;
                } else {
                    // Check if it's a comment
                    [$commentPostCol, $commentUserCol, $commentPkCol] = $this->resolvePostCommentsColumns();
                    $comment = DB::table('post_comments')
                        ->where($commentPkCol, $refId)
                        ->first();
                    if ($comment) {
                        $postId = $comment->{$commentPostCol} ?? null;
                    }
                }
            }

            if (!$postId) {
                return null;
            }

            // Posts table uses post_id as primary key, not id
            $post = DB::table('posts')
                ->where('post_id', $postId)
                ->first();

            if (!$post) {
                return null;
            }

            return [
                'id' => $postId,
                'title' => $post->post_header ?? $post->post_title ?? 'Post',
                'category' => $post->category ?? $post->section ?? null, // 'Announcement' or 'Discussion'
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting post details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get action text based on notification type
     */
    private function getActionText($notification, $actor, $post)
    {
        $postTitle = $post ? $post['title'] : 'your post';
        $refId = $notification->notification_reference_id;
        $type = $notification->notification_type;

        // For Report type (restrictions), use the message directly
        if ($type === 'Report' && $notification->message) {
            return $notification->message;
        }
        
        // For DocumentRequest and EventAssistance types, use the message directly
        if (($type === 'DocumentRequest' || $type === 'EventAssistance') && $notification->message) {
            return $notification->message;
        }

        // For Comment type, check if it's actually a reaction or a comment
        if ($type === 'Comment' && $refId) {
            // First check if it's a reaction
            [$postCol, $userCol, $pkCol] = $this->resolvePostReactionsColumns();
            $reaction = DB::table('post_reactions')
                ->where($pkCol, $refId)
                ->first();

            if ($reaction && isset($reaction->reaction_type)) {
                $reactionType = $reaction->reaction_type;
                if ($reactionType === 'Dislike') {
                    return "disliked your post";
                }
                return "liked your post";
            }

            // If not a reaction, it's a comment
            return "commented on {$postTitle}";
        }

        switch ($type) {
            case 'Comment':
                return "commented on {$postTitle}";

            case 'Post':
                return "created a new post: {$postTitle}";

            default:
                return "interacted with {$postTitle}";
        }
    }

    /**
     * Format time as relative time (e.g., "2 hours ago")
     */
    private function formatTime($timestamp)
    {
        try {
            $time = Carbon::parse($timestamp);
            $now = Carbon::now();
            $diff = $time->diffInSeconds($now);

            if ($diff < 60) {
                return 'just now';
            } elseif ($diff < 3600) {
                $minutes = floor($diff / 60);
                return "{$minutes} minute" . ($minutes > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                return "{$hours} hour" . ($hours > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 604800) {
                $days = floor($diff / 86400);
                return "{$days} day" . ($days > 1 ? 's' : '') . ' ago';
            } else {
                return $time->format('M d, Y');
            }
        } catch (\Exception $e) {
            return 'recently';
        }
    }

    /**
     * Get avatar URL
     */
    private function getAvatar($actor)
    {
        return $actor['avatar'] ?? '/assets/DEFAULT.jpg';
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $userId = $user->user_id ?? $user->id;

            DB::table('notifications')
                ->where('notification_id', $id)
                ->where('fk_user_id', $userId)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $userId = $user->user_id ?? $user->id;

            DB::table('notifications')
                ->where('fk_user_id', $userId)
                ->whereIn('notification_type', ['Post', 'Comment', 'Report', 'DocumentRequest', 'EventAssistance'])
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

