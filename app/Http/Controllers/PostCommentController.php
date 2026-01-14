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

                $comment = [
                    'id' => $row->id,
                    'author' => (!empty(trim($row->author_name ?? '')) ? $row->author_name : 'Unknown'),
                    'avatar' => $avatar,
                    'date' => $createdAt->toISOString(),
                    'text' => $row->comment_text ?? '',
                    'likes' => 0,
                    'dislikes' => 0,
                    'userLiked' => false,
                    'userDisliked' => false,
                    'replies' => [],
                ];

                $commentsById[$row->id] = $comment;
            }

            // Attach replies
            foreach ($rows as $row) {
                $parentId = $row->parent_id ?? null;
                if ($parentId) {
                    if (isset($commentsById[$parentId]) && isset($commentsById[$row->id])) {
                        $commentsById[$parentId]['replies'][] = $commentsById[$row->id];
                    }
                } else {
                    if (isset($commentsById[$row->id])) {
                        $rootComments[] = $commentsById[$row->id];
                    }
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
}


