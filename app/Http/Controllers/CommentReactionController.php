<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CommentReactionController extends Controller
{
    private function resolveCommentReactionsColumns(): array
    {
        $commentCol = Schema::hasColumn('comment_reactions', 'fk_comment_id') ? 'fk_comment_id' : 'comment_id';
        $userCol = Schema::hasColumn('comment_reactions', 'fk_user_id') ? 'fk_user_id' : 'user_id';
        $pkCol = Schema::hasColumn('comment_reactions', 'comment_reaction_id') ? 'comment_reaction_id' : 'id';

        return [$commentCol, $userCol, $pkCol];
    }

    /**
     * Toggle like / dislike reaction for a comment or reply.
     *
     * Request payload:
     *  - reaction_type: 'Like' | 'Dislike'
     */
    public function toggle(Request $request, $commentId)
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
            [$commentCol, $userCol, $pkCol] = $this->resolveCommentReactionsColumns();

            $existing = DB::table('comment_reactions')
                ->where($commentCol, $commentId)
                ->where($userCol, $userId)
                ->first();

            $reactionId = null;
            if ($existing) {
                if ($existing->reaction_type === $validated['reaction_type']) {
                    // Same reaction → remove (unlike / undislike)
                    DB::table('comment_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->delete();
                } else {
                    // Different reaction → switch type
                    $reactionId = $existing->{$pkCol};
                    DB::table('comment_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->update([
                            'reaction_type' => $validated['reaction_type'],
                            'created_at' => now(),
                        ]);
                }
            } else {
                // No reaction yet → create new
                if ($pkCol === 'id') {
                    $reactionId = DB::table('comment_reactions')->insertGetId([
                        $commentCol => $commentId,
                        $userCol => $userId,
                        'reaction_type' => $validated['reaction_type'],
                        'created_at' => now(),
                    ]);
                } else {
                    DB::table('comment_reactions')->insert([
                        $commentCol => $commentId,
                        $userCol => $userId,
                        'reaction_type' => $validated['reaction_type'],
                        'created_at' => now(),
                    ]);
                    // Get the inserted ID
                    $reactionId = DB::table('comment_reactions')
                        ->where($commentCol, $commentId)
                        ->where($userCol, $userId)
                        ->where('reaction_type', $validated['reaction_type'])
                        ->value($pkCol);
                }
            }

            // Recalculate counts
            $likes = DB::table('comment_reactions')
                ->where($commentCol, $commentId)
                ->where('reaction_type', 'Like')
                ->count();

            $dislikes = DB::table('comment_reactions')
                ->where($commentCol, $commentId)
                ->where('reaction_type', 'Dislike')
                ->count();

            // Determine current user state after the operation
            $userReaction = DB::table('comment_reactions')
                ->where($commentCol, $commentId)
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
            Log::error('Error toggling comment reaction: ' . $e->getMessage(), [
                'comment_id' => $commentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle reaction.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

