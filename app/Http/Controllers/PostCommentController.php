<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PostCommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'comment_text' => 'required|string|max:1000',
                'parent_comment_id' => 'nullable|integer',
            ]);

            $userId = $user->user_id ?? $user->id;

            $commentId = DB::table('post_comments')->insertGetId([
                'fk_post_id' => $postId,
                'fk_commenter_id' => $userId,
                'fk_parent_comment_id' => $validated['parent_comment_id'] ?? null,
                'comment_text' => $validated['comment_text'],
                'created_at' => now(),
            ]);

            // Get the created comment with author info
            $comment = DB::table('post_comments')
                ->join('users', 'post_comments.fk_commenter_id', '=', 'users.user_id')
                ->where('post_comments.comment_id', $commentId)
                ->select(
                    'post_comments.comment_id as id',
                    'post_comments.comment_text as text',
                    'post_comments.created_at',
                    'post_comments.fk_parent_comment_id as parent_comment_id',
                    'users.first_name',
                    'users.last_name',
                    'users.profile_pic'
                )
                ->first();

            if (!$comment) {
                Log::error('Comment not found after creation: ' . $commentId);
                return response()->json(['error' => 'Failed to retrieve comment'], 500);
            }

            // Build full name from first_name and last_name
            $authorName = trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? ''));
            if (empty($authorName)) {
                $authorName = 'Unknown';
            }

            $avatar = '/assets/DEFAULT.jpg';
            if ($comment->profile_pic) {
                $avatar = $comment->profile_pic;
                if (!str_starts_with($avatar, 'http') && !str_starts_with($avatar, '/storage/')) {
                    $avatar = '/storage/' . $avatar;
                }
            }

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'author' => $authorName,
                    'avatar' => $avatar,
                    'text' => $comment->text,
                    'date' => Carbon::parse($comment->created_at)->toISOString(),
                    'time' => Carbon::parse($comment->created_at)->format('g:i A'),
                    'parent_comment_id' => $comment->parent_comment_id,
                    'replies' => [],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error storing comment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store comment'], 500);
        }
    }

    public function getComments($postId)
    {
        try {
            $comments = DB::table('post_comments')
                ->join('users', 'post_comments.fk_commenter_id', '=', 'users.user_id')
                ->where('post_comments.fk_post_id', $postId)
                ->whereNull('post_comments.fk_parent_comment_id') // Only top-level comments
                ->select(
                    'post_comments.comment_id as id',
                    'post_comments.comment_text as text',
                    'post_comments.created_at',
                    'users.first_name',
                    'users.last_name',
                    'users.profile_pic',
                    'users.fk_role_id'
                )
                ->orderBy('post_comments.created_at', 'asc')
                ->get()
                ->map(function ($comment) {
                    // Build full name from first_name and last_name
                    $authorName = trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? ''));
                    if (empty($authorName)) {
                        $authorName = 'Unknown';
                    }

                    $avatar = '/assets/DEFAULT.jpg';
                    if ($comment->profile_pic) {
                        $avatar = $comment->profile_pic;
                        if (!str_starts_with($avatar, 'http') && !str_starts_with($avatar, '/storage/')) {
                            $avatar = '/storage/' . $avatar;
                        }
                    }

                    // Get replies for this comment
                    $replies = DB::table('post_comments')
                        ->join('users', 'post_comments.fk_commenter_id', '=', 'users.user_id')
                        ->where('post_comments.fk_parent_comment_id', $comment->id)
                        ->select(
                            'post_comments.comment_id as id',
                            'post_comments.comment_text as text',
                            'post_comments.created_at',
                            'users.first_name',
                            'users.last_name',
                            'users.profile_pic'
                        )
                        ->orderBy('post_comments.created_at', 'asc')
                        ->get()
                        ->map(function ($reply) {
                            // Build full name from first_name and last_name
                            $replyAuthorName = trim(($reply->first_name ?? '') . ' ' . ($reply->last_name ?? ''));
                            if (empty($replyAuthorName)) {
                                $replyAuthorName = 'Unknown';
                            }

                            $replyAvatar = '/assets/DEFAULT.jpg';
                            if ($reply->profile_pic) {
                                $replyAvatar = $reply->profile_pic;
                                if (!str_starts_with($replyAvatar, 'http') && !str_starts_with($replyAvatar, '/storage/')) {
                                    $replyAvatar = '/storage/' . $replyAvatar;
                                }
                            }
                            return [
                                'id' => $reply->id,
                                'author' => $replyAuthorName,
                                'avatar' => $replyAvatar,
                                'text' => $reply->text,
                                'date' => Carbon::parse($reply->created_at)->toISOString(),
                                'time' => Carbon::parse($reply->created_at)->format('g:i A'),
                            ];
                        })
                        ->toArray();

                    return [
                        'id' => $comment->id,
                        'author' => $authorName,
                        'avatar' => $avatar,
                        'text' => $comment->text,
                        'date' => Carbon::parse($comment->created_at)->toISOString(),
                        'time' => Carbon::parse($comment->created_at)->format('g:i A'),
                        'replies' => $replies,
                    ];
                });

            return response()->json([
                'success' => true,
                'comments' => $comments,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting comments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get comments'], 500);
        }
    }

    public function getCommentCount($postId)
    {
        try {
            $count = DB::table('post_comments')
                ->where('fk_post_id', $postId)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting comment count: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get comment count'], 500);
        }
    }
}

