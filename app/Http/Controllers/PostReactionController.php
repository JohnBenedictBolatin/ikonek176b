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

            if ($existing) {
                if ($existing->reaction_type === $validated['reaction_type']) {
                    // Same reaction → remove (unlike / undislike)
                    DB::table('post_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->delete();
                } else {
                    // Different reaction → switch type
                    DB::table('post_reactions')
                        ->where($pkCol, $existing->{$pkCol})
                        ->update([
                            'reaction_type' => $validated['reaction_type'],
                            'created_at' => now(),
                        ]);
                }
            } else {
                // No reaction yet → create new
                DB::table('post_reactions')->insert([
                    $postCol => $postId,
                    $userCol => $userId,
                    'reaction_type' => $validated['reaction_type'],
                    'created_at' => now(),
                ]);
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


