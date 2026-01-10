<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostReactionController extends Controller
{
    public function toggle(Request $request, $postId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = $user->user_id ?? $user->id;
            $reactionType = $request->input('reaction_type'); // 'Like' or 'Dislike'

            if (!in_array($reactionType, ['Like', 'Dislike'])) {
                return response()->json(['error' => 'Invalid reaction type'], 400);
            }

            // Check if user already has a reaction for this post
            $existingReaction = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('fk_reactor_id', $userId)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->reaction_type === $reactionType) {
                    // Remove the reaction (toggle off)
                    DB::table('post_reactions')
                        ->where('post_reaction_id', $existingReaction->post_reaction_id)
                        ->delete();
                    
                    $action = 'removed';
                } else {
                    // Update to the opposite reaction type
                    DB::table('post_reactions')
                        ->where('post_reaction_id', $existingReaction->post_reaction_id)
                        ->update(['reaction_type' => $reactionType]);
                    
                    $action = 'updated';
                }
            } else {
                // Create new reaction
                DB::table('post_reactions')->insert([
                    'fk_post_id' => $postId,
                    'fk_reactor_id' => $userId,
                    'reaction_type' => $reactionType,
                    'created_at' => now(),
                ]);
                
                $action = 'added';
            }

            // Get updated counts
            $likes = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('reaction_type', 'Like')
                ->count();
            
            $dislikes = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('reaction_type', 'Dislike')
                ->count();

            // Check user's current reaction
            $userReaction = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('fk_reactor_id', $userId)
                ->first();

            return response()->json([
                'success' => true,
                'action' => $action,
                'likes' => $likes,
                'dislikes' => $dislikes,
                'userLiked' => $userReaction && $userReaction->reaction_type === 'Like',
                'userDisliked' => $userReaction && $userReaction->reaction_type === 'Dislike',
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling reaction: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to toggle reaction'], 500);
        }
    }

    public function getReactions($postId)
    {
        try {
            $user = Auth::user();
            $userId = $user ? ($user->user_id ?? $user->id) : null;

            $likes = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('reaction_type', 'Like')
                ->count();
            
            $dislikes = DB::table('post_reactions')
                ->where('fk_post_id', $postId)
                ->where('reaction_type', 'Dislike')
                ->count();

            $userLiked = false;
            $userDisliked = false;

            if ($userId) {
                $userReaction = DB::table('post_reactions')
                    ->where('fk_post_id', $postId)
                    ->where('fk_reactor_id', $userId)
                    ->first();

                if ($userReaction) {
                    $userLiked = $userReaction->reaction_type === 'Like';
                    $userDisliked = $userReaction->reaction_type === 'Dislike';
                }
            }

            return response()->json([
                'likes' => $likes,
                'dislikes' => $dislikes,
                'userLiked' => $userLiked,
                'userDisliked' => $userDisliked,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting reactions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get reactions'], 500);
        }
    }
}









