<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PostCommentController extends Controller
{
    private function resolvePostCommentsColumns(): array
    {
        $postCol = Schema::hasColumn('post_comments', 'fk_post_id') ? 'fk_post_id' : 'post_id';
        $userCol = Schema::hasColumn('post_comments', 'fk_commenter_id') ? 'fk_commenter_id' : 'user_id';
        $parentCol = Schema::hasColumn('post_comments', 'fk_parent_comment_id')
            ? 'fk_parent_comment_id'
            : (Schema::hasColumn('post_comments', 'parent_comment_id') ? 'parent_comment_id' : null);
        $pkCol = Schema::hasColumn('post_comments', 'comment_id') ? 'comment_id' : 'id';

        return [$postCol, $userCol, $parentCol, $pkCol];
    }

    private function resolveUserDisplayNameColumns(): array
    {
        $hasName = Schema::hasColumn('users', 'name');
        $hasFirst = Schema::hasColumn('users', 'first_name');
        $hasLast = Schema::hasColumn('users', 'last_name');
        $hasAvatar = Schema::hasColumn('users', 'avatar');
        $hasProfilePic = Schema::hasColumn('users', 'profile_pic');

        return [$hasName, $hasFirst, $hasLast, $hasAvatar, $hasProfilePic];
    }

    private function resolveCommentReactionsColumns(): array
    {
        $commentCol = Schema::hasColumn('comment_reactions', 'fk_comment_id') ? 'fk_comment_id' : 'comment_id';
        $userCol = Schema::hasColumn('comment_reactions', 'fk_user_id') ? 'fk_user_id' : 'user_id';
        
        return [$commentCol, $userCol];
    }

    /**
     * Store a new comment or reply.
     *
     * Body:
     *  - comment_text (string, required)
     *  - parent_comment_id (nullable, for replies)
     */
    public function store(Request $request, $postId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            // Check if user is restricted from commenting
            $userId = $user->user_id ?? $user->id;
            $restriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
            if ($restriction && $restriction->restrict_commenting) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are restricted from commenting. Please check your notifications for more information.',
                ], 403);
            }

            $validated = $request->validate([
                'comment_text' => 'required|string',
                'parent_comment_id' => 'nullable|integer',
            ]);

            $userId = $user->user_id ?? $user->id;

            [$postCol, $userCol, $parentCol, $pkCol] = $this->resolvePostCommentsColumns();

            $now = now();
            $insert = [
                $postCol => $postId,
                $userCol => $userId,
                'comment_text' => $validated['comment_text'],
                'created_at' => $now,
            ];
            if ($parentCol) {
                $insert[$parentCol] = $validated['parent_comment_id'] ?? null;
            }

            // insertGetId needs the correct PK column name when it's non-standard
            $id = $pkCol === 'id'
                ? DB::table('post_comments')->insertGetId($insert)
                : DB::table('post_comments')->insertGetId($insert, $pkCol);

            // Create notification for post author (only for top-level comments, not replies)
            if (!$validated['parent_comment_id']) {
                try {
                    $this->createCommentNotification($postId, $id, $userId);
                } catch (\Exception $e) {
                    Log::error('Error calling createCommentNotification: ' . $e->getMessage());
                }
            }

            $createdAt = Carbon::parse($now);

            // Normalize response structure expected by the Vue components
            $avatar = $user->profile_pic
                ? (str_starts_with($user->profile_pic, 'http') || str_starts_with($user->profile_pic, '/storage/')
                    ? $user->profile_pic
                    : '/storage/' . $user->profile_pic)
                : ($user->avatar ?? '/assets/DEFAULT.jpg');

            [$hasName, $hasFirst, $hasLast] = $this->resolveUserDisplayNameColumns();
            $displayName = $user->name ?? null;
            if (!$displayName && ($hasFirst || $hasLast)) {
                $first = $user->first_name ?? '';
                $last = $user->last_name ?? '';
                $displayName = trim($first . ' ' . $last) ?: 'Unknown';
            }

            $commentPayload = [
                'id' => $id,
                'author' => $displayName ?? 'Unknown',
                'avatar' => $avatar,
                'date' => $createdAt->toISOString(),
                'text' => $validated['comment_text'],
                'likes' => 0,
                'dislikes' => 0,
                'userLiked' => false,
                'userDisliked' => false,
                'replies' => [],
            ];

            return response()->json([
                'success' => true,
                'comment' => $commentPayload,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Error storing comment: ' . $e->getMessage(), [
                'post_id' => $postId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get all comments (with nested replies) for a post.
     */
    public function getComments($postId)
    {
        try {
            [$postCol, $userCol, $parentCol, $pkCol] = $this->resolvePostCommentsColumns();
            [$hasName, $hasFirst, $hasLast, $hasAvatar, $hasProfilePic] = $this->resolveUserDisplayNameColumns();

            $query = DB::table('post_comments')
                ->join('users', "post_comments.$userCol", '=', 'users.user_id')
                ->where("post_comments.$postCol", $postId)
                ->orderBy('post_comments.created_at', 'asc');

            // Select PK + parent + content + created + user identity fields.
            $select = [
                "post_comments.$pkCol as id",
                "post_comments.$userCol as author_id",
                'post_comments.comment_text',
                'post_comments.created_at',
            ];
            if ($hasProfilePic) {
                $select[] = 'users.profile_pic';
            } else {
                $select[] = DB::raw('NULL as profile_pic');
            }
            if ($hasAvatar) {
                $select[] = 'users.avatar';
            } else {
                $select[] = DB::raw('NULL as avatar');
            }
            if ($parentCol) {
                $select[] = "post_comments.$parentCol as parent_id";
            } else {
                $select[] = DB::raw('NULL as parent_id');
            }

            if ($hasName) {
                $select[] = 'users.name as author_name';
            } elseif ($hasFirst || $hasLast) {
                // MySQL CONCAT_WS handles NULLs nicely
                $select[] = DB::raw("TRIM(CONCAT_WS(' ', users.first_name, users.last_name)) as author_name");
            } else {
                $select[] = DB::raw("'Unknown' as author_name");
            }

            $rows = $query->select($select)->get();

            // Build nested structure (top-level comments + replies)
            $commentsById = [];
            $rootComments = [];

            foreach ($rows as $row) {
                $createdAt = $row->created_at ? Carbon::parse($row->created_at) : now();

                $profilePic = $row->profile_pic ?? null;
                $avatarField = $row->avatar ?? null;
                $avatar = $profilePic
                    ? (str_starts_with($profilePic, 'http') || str_starts_with($profilePic, '/storage/')
                        ? $profilePic
                        : '/storage/' . $profilePic)
                    : ($avatarField ?? '/assets/DEFAULT.jpg');

                // Normalize ID to int for consistent lookups
                $commentId = (int)$row->id;
                
                $comment = [
                    'id' => $commentId,
                    'author' => (!empty(trim($row->author_name ?? '')) ? $row->author_name : 'Unknown'),
                    'author_id' => $row->author_id ?? null,
                    'avatar' => $avatar,
                    'date' => $createdAt->toISOString(),
                    'text' => $row->comment_text ?? '',
                    'likes' => 0,
                    'dislikes' => 0,
                    'userLiked' => false,
                    'userDisliked' => false,
                    'replies' => [],
                ];

                $commentsById[$commentId] = $comment;
            }

            // Attach replies (use references so reaction updates propagate)
            foreach ($rows as $row) {
                $parentId = $row->parent_id ? (int)$row->parent_id : null;
                $commentId = (int)$row->id;
                
                if ($parentId) {
                    if (isset($commentsById[$parentId]) && isset($commentsById[$commentId])) {
                        $commentsById[$parentId]['replies'][] = &$commentsById[$commentId];
                    }
                } else {
                    if (isset($commentsById[$commentId])) {
                        $rootComments[] = &$commentsById[$commentId];
                    }
                }
            }

            // Load reaction counts for all comments (including replies)
            $authUser = Auth::user();
            $userId = $authUser ? ($authUser->user_id ?? $authUser->id) : null;
            
            $allCommentIds = array_keys($commentsById);
            if (!empty($allCommentIds) && Schema::hasTable('comment_reactions')) {
                try {
                    [$commentCol, $userCol] = $this->resolveCommentReactionsColumns();
                    
                    // Get all reaction counts
                    $reactionCountsRaw = DB::table('comment_reactions')
                        ->whereIn($commentCol, $allCommentIds)
                        ->select($commentCol . ' as comment_id', 'reaction_type', DB::raw('count(*) as count'))
                        ->groupBy($commentCol, 'reaction_type')
                        ->get();
                    
                    // Group by comment_id, ensuring consistent key types
                    $reactionCounts = [];
                    foreach ($reactionCountsRaw as $reaction) {
                        $id = (int)$reaction->comment_id; // Normalize to int
                        if (!isset($reactionCounts[$id])) {
                            $reactionCounts[$id] = collect();
                        }
                        $reactionCounts[$id]->push($reaction);
                    }

                    // Get user reactions
                    $userReactions = [];
                    if ($userId) {
                        $userReactionsRaw = DB::table('comment_reactions')
                            ->whereIn($commentCol, $allCommentIds)
                            ->where($userCol, $userId)
                            ->select($commentCol, 'reaction_type')
                            ->get();
                        
                        foreach ($userReactionsRaw as $reaction) {
                            $id = (int)$reaction->{$commentCol}; // Normalize to int
                            $userReactions[$id] = $reaction->reaction_type;
                        }
                    }

                    // Apply reaction counts to all comments and replies
                    foreach ($commentsById as $commentId => &$comment) {
                        // Normalize commentId to int for consistent lookup
                        $normalizedId = (int)$commentId;
                        
                        // Get reaction counts for this comment
                        $counts = $reactionCounts[$normalizedId] ?? collect();
                        
                        $likeCount = $counts->where('reaction_type', 'Like')->sum('count');
                        $dislikeCount = $counts->where('reaction_type', 'Dislike')->sum('count');
                        
                        $comment['likes'] = is_numeric($likeCount) ? (int)$likeCount : 0;
                        $comment['dislikes'] = is_numeric($dislikeCount) ? (int)$dislikeCount : 0;
                        
                        // Get user reaction for this comment
                        $userReaction = $userReactions[$normalizedId] ?? null;
                        $comment['userLiked'] = $userReaction === 'Like';
                        $comment['userDisliked'] = $userReaction === 'Dislike';
                    }
                    unset($comment);
                    
                    // Explicitly update root comments and nested replies to ensure data is synced
                    // (References might not work properly when serialized to JSON)
                    foreach ($rootComments as &$rootComment) {
                        $rootCommentId = isset($rootComment['id']) ? (int)$rootComment['id'] : null;
                        if ($rootCommentId && isset($commentsById[$rootCommentId])) {
                            // Update root comment reaction data
                            $rootComment['likes'] = $commentsById[$rootCommentId]['likes'];
                            $rootComment['dislikes'] = $commentsById[$rootCommentId]['dislikes'];
                            $rootComment['userLiked'] = $commentsById[$rootCommentId]['userLiked'];
                            $rootComment['userDisliked'] = $commentsById[$rootCommentId]['userDisliked'];
                            
                            // Update nested replies
                            if (isset($rootComment['replies']) && is_array($rootComment['replies'])) {
                                foreach ($rootComment['replies'] as &$reply) {
                                    $replyId = isset($reply['id']) ? (int)$reply['id'] : null;
                                    if ($replyId && isset($commentsById[$replyId])) {
                                        $reply['likes'] = $commentsById[$replyId]['likes'];
                                        $reply['dislikes'] = $commentsById[$replyId]['dislikes'];
                                        $reply['userLiked'] = $commentsById[$replyId]['userLiked'];
                                        $reply['userDisliked'] = $commentsById[$replyId]['userDisliked'];
                                    }
                                }
                                unset($reply);
                            }
                        }
                    }
                    unset($rootComment);
                } catch (\Exception $e) {
                    Log::error('Error loading comment reactions: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'comments' => $rootComments,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Error fetching comments: ' . $e->getMessage(), [
                'post_id' => $postId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load comments.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'comments' => [],
            ], 500);
        }
    }

    /**
     * Simple comment count endpoint for a post.
     */
    public function getCommentCount($postId)
    {
        [$postCol] = $this->resolvePostCommentsColumns();
        $count = DB::table('post_comments')
            ->where($postCol, $postId)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Create notification for post author when someone comments on their post
     */
    private function createCommentNotification($postId, $commentId, $commenterId)
    {
        try {
            // Get post author - posts table uses post_id as primary key
            $post = DB::table('posts')
                ->where('post_id', $postId)
                ->first();

            if (!$post) {
                return;
            }

            $postAuthorId = $post->fk_post_author_id ?? $post->user_id ?? null;
            
            // Allow notifications even for own posts (for testing)
            if (!$postAuthorId) {
                return;
            }

            // Check if notification already exists for this comment
            // Actual table uses: fk_user_id, notification_type, notification_reference_id
            $existingNotification = DB::table('notifications')
                ->where('fk_user_id', $postAuthorId)
                ->where('notification_type', 'Comment')
                ->where('notification_reference_id', $commentId)
                ->first();

            if ($existingNotification) {
                return; // Notification already exists
            }

            // Create notification - use actual column names from database
            try {
                $notificationId = DB::table('notifications')->insertGetId([
                    'fk_user_id' => $postAuthorId,
                    'notification_type' => 'Comment',
                    'notification_reference_id' => $commentId,
                    'message' => '', // Required field
                    'is_read' => false,
                    'created_at' => now(),
                ]);
                
                Log::info('Comment notification created', [
                    'notification_id' => $notificationId,
                    'user_id' => $postAuthorId,
                    'comment_id' => $commentId,
                    'post_id' => $postId
                ]);
            } catch (\Exception $insertError) {
                Log::error('Failed to insert comment notification', [
                    'error' => $insertError->getMessage(),
                    'user_id' => $postAuthorId,
                    'comment_id' => $commentId,
                    'post_id' => $postId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating comment notification: ' . $e->getMessage(), [
                'post_id' => $postId,
                'comment_id' => $commentId,
                'post_author_id' => $postAuthorId ?? 'null'
            ]);
        }
    }

    /**
     * Delete a comment (only by the author)
     */
    public function destroy($commentId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            [$postCol, $userCol, $parentCol, $pkCol] = $this->resolvePostCommentsColumns();
            $userId = $user->user_id ?? $user->id;

            // Get the comment
            $comment = DB::table('post_comments')
                ->where($pkCol, $commentId)
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found.',
                ], 404);
            }

            $commentAuthorId = $comment->$userCol ?? null;

            // Check if user is the author
            if ($userId != $commentAuthorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own comments.',
                ], 403);
            }

            // Delete the comment (cascade should handle replies if configured)
            DB::table('post_comments')
                ->where($pkCol, $commentId)
                ->delete();

            Log::info('Comment deleted by author', [
                'comment_id' => $commentId,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting comment: ' . $e->getMessage(), [
                'comment_id' => $commentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}


