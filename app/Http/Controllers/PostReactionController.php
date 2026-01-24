<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PostReactionController extends Controller
{
    private function resolvePostReactionsColumns(): array
    {
        $postCol = Schema::hasColumn('post_reactions', 'fk_post_id') ? 'fk_post_id' : 'post_id';
        $userCol = Schema::hasColumn('post_reactions', 'fk_reactor_id') ? 'fk_reactor_id' : 'user_id';
        $pkCol = Schema::hasColumn('post_reactions', 'post_reaction_id') ? 'post_reaction_id' : 'id';

        return [$postCol, $userCol, $pkCol];
    }

    /**
     * Toggle like / dislike reaction for an authenticated user.
     *
     * Request payload:
     *  - reaction_type: 'Like' | 'Dislike'
     */
    public function toggle(Request $request, $postId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $validated = $request->validate([
                'reaction_type' => 'required|in:Like,Dislike',
            ]);

            $userId = $user->user_id ?? $user->id;

            // Support both schemas & primary key names
            [$postCol, $userCol, $pkCol] = $this->resolvePostReactionsColumns();

            $existing = DB::table('post_reactions')
                ->where($postCol, $postId)
                ->where($userCol, $userId)
                ->first();

            $reactionId = null;
            if ($existing) {
                if ($existing->reaction_type === $validated['reaction_type']) {
                    // Same reaction → remove (unlike / undislike)
                    DB::table('post_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->delete();
                } else {
                    // Different reaction → switch type
                    $reactionId = $existing->{$pkCol};
                    DB::table('post_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->update([
                            'reaction_type' => $validated['reaction_type'],
                            'created_at' => now(),
                        ]);
                }
            } else {
                // No reaction yet → create new
                if ($pkCol === 'id') {
                    $reactionId = DB::table('post_reactions')->insertGetId([
                        $postCol => $postId,
                        $userCol => $userId,
                        'reaction_type' => $validated['reaction_type'],
                        'created_at' => now(),
                    ]);
                } else {
                    DB::table('post_reactions')->insert([
                        $postCol => $postId,
                        $userCol => $userId,
                        'reaction_type' => $validated['reaction_type'],
                        'created_at' => now(),
                    ]);
                    // Get the inserted ID
                    $reactionId = DB::table('post_reactions')
                        ->where($postCol, $postId)
                        ->where($userCol, $userId)
                        ->where('reaction_type', $validated['reaction_type'])
                        ->value($pkCol);
                }
            }

            // Create notification for post author (only if reaction was added, not removed)
            if ($reactionId) {
                try {
                    $this->createReactionNotification($postId, $reactionId, $userId);
                } catch (\Exception $e) {
                    \Log::error('Error calling createReactionNotification: ' . $e->getMessage());
                }
            }

            // Recalculate counts
            $likes = DB::table('post_reactions')
                ->where($postCol, $postId)
                ->where('reaction_type', 'Like')
                ->count();

            $dislikes = DB::table('post_reactions')
                ->where($postCol, $postId)
                ->where('reaction_type', 'Dislike')
                ->count();

            // Determine current user state after the operation
            $userReaction = DB::table('post_reactions')
                ->where($postCol, $postId)
                ->where($userCol, $userId)
                ->first();

            $userLiked = $userReaction && $userReaction->reaction_type === 'Like';
            $userDisliked = $userReaction && $userReaction->reaction_type === 'Dislike';

            return response()->json([
                'success' => true,
                'likes' => $likes,
                'dislikes' => $dislikes,
                'userLiked' => $userLiked,
                'userDisliked' => $userDisliked,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle reaction.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Create notification for post author when someone reacts to their post
     */
    private function createReactionNotification($postId, $reactionId, $reactorId)
    {
        try {
            \Log::info('createReactionNotification called', [
                'post_id' => $postId,
                'reaction_id' => $reactionId,
                'reactor_id' => $reactorId
            ]);

            // Get post author - posts table uses post_id as primary key
            $post = DB::table('posts')
                ->where('post_id', $postId)
                ->first();

            if (!$post) {
                \Log::warning('Post not found for notification', ['post_id' => $postId]);
                return;
            }

            $postAuthorId = $post->fk_post_author_id ?? $post->user_id ?? null;
            
            \Log::info('Post author found', [
                'post_author_id' => $postAuthorId,
                'post_id' => $postId,
                'fk_post_author_id' => $post->fk_post_author_id ?? 'null',
                'user_id' => $post->user_id ?? 'null'
            ]);
            
            // Allow notifications even for own posts (for testing)
            if (!$postAuthorId) {
                \Log::warning('No post author ID found', ['post_id' => $postId]);
                return;
            }

            // Check if notification already exists for this reaction
            // Actual table uses: fk_user_id, notification_type, notification_reference_id
            $existingNotification = DB::table('notifications')
                ->where('fk_user_id', $postAuthorId)
                ->where('notification_type', 'Comment') // Using Comment as closest match for reactions
                ->where('notification_reference_id', $reactionId)
                ->first();

            if ($existingNotification) {
                return; // Notification already exists
            }

            // Create notification - use actual column names from database
            try {
                $notificationId = DB::table('notifications')->insertGetId([
                    'fk_user_id' => $postAuthorId,
                    'notification_type' => 'Comment', // Using Comment type for reactions (closest match)
                    'notification_reference_id' => $reactionId,
                    'message' => '', // Required field
                    'is_read' => false,
                    'created_at' => now(),
                ]);
                
                \Log::info('Reaction notification created', [
                    'notification_id' => $notificationId,
                    'user_id' => $postAuthorId,
                    'reaction_id' => $reactionId,
                    'post_id' => $postId
                ]);
            } catch (\Exception $insertError) {
                \Log::error('Failed to insert reaction notification', [
                    'error' => $insertError->getMessage(),
                    'user_id' => $postAuthorId,
                    'reaction_id' => $reactionId,
                    'post_id' => $postId
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating reaction notification: ' . $e->getMessage(), [
                'post_id' => $postId,
                'reaction_id' => $reactionId,
                'post_author_id' => $postAuthorId ?? 'null'
            ]);
        }
    }

    /**
     * Optional: return current reactions summary for a post.
     * Currently not used by the frontend but kept for completeness.
     */
    public function getReactions($postId)
    {
        $user = Auth::user();
        $userId = $user ? ($user->user_id ?? $user->id) : null;

        $postCol = Schema::hasColumn('post_reactions', 'fk_post_id') ? 'fk_post_id' : 'post_id';
        $userCol = Schema::hasColumn('post_reactions', 'fk_reactor_id') ? 'fk_reactor_id' : 'user_id';

        $likes = DB::table('post_reactions')
            ->where($postCol, $postId)
            ->where('reaction_type', 'Like')
            ->count();

        $dislikes = DB::table('post_reactions')
            ->where($postCol, $postId)
            ->where('reaction_type', 'Dislike')
            ->count();

        $userLiked = false;
        $userDisliked = false;

        if ($userId) {
            $userReaction = DB::table('post_reactions')
                ->where($postCol, $postId)
                ->where($userCol, $userId)
                ->first();

            if ($userReaction) {
                $userLiked = $userReaction->reaction_type === 'Like';
                $userDisliked = $userReaction->reaction_type === 'Dislike';
            }
        }

        return response()->json([
            'success' => true,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'userLiked' => $userLiked,
            'userDisliked' => $userDisliked,
        ]);
    }
}


