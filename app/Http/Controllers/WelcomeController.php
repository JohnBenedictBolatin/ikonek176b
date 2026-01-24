<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
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

        // Fetch recent announcements (limit 5)
        $announcements = Post::with(['author', 'tags'])
            ->where('section', 'Announcement')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($post) use ($roleMap) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                $authorRole = 'Resident';

                if ($post->author) {
                    $authorName = $post->author->name ?? 'Unknown User';
                    if ($post->author->profile_pic) {
                        $authorAvatar = $post->author->profile_pic;
                        if (!str_starts_with($authorAvatar, 'http') && !str_starts_with($authorAvatar, '/storage/')) {
                            $authorAvatar = '/storage/' . $authorAvatar;
                        }
                    } else {
                        $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';
                    }
                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';
                }

                $createdAt = $post->created_at ? Carbon::parse($post->created_at) : now();

                $images = [];
                if (!empty($post->image_content)) {
                    $images[] = asset('storage/' . $post->image_content);
                }

                $tags = $post->tags && $post->tags->count() ? $post->tags->pluck('tag_name')->toArray() : [];

                $postHeader = null;
                if (Schema::hasColumn('posts', 'header')) {
                    try {
                        $headerValue = $post->header ?? null;
                        $postHeader = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
                    } catch (\Exception $e) {
                        $postHeader = null;
                    }
                }

                return [
                    'id' => $post->post_id,
                    'author' => $authorName,
                    'avatar' => $authorAvatar,
                    'role' => $authorRole,
                    'header' => $postHeader,
                    'content' => $post->content ?? '',
                    'images' => $images,
                    'tags' => $tags,
                    'date' => $createdAt->format('M d, Y'),
                    'time' => $createdAt->format('g:i A'),
                    'likes' => DB::table('post_reactions')
                        ->where('fk_post_id', $post->post_id)
                        ->where('reaction_type', 'Like')
                        ->count(),
                    'comments' => DB::table('post_comments')
                        ->where('fk_post_id', $post->post_id)
                        ->count(),
                ];
            });

        // Fetch recent discussions (limit 5)
        $discussions = Post::with(['author', 'tags'])
            ->where('section', 'Discussion')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($post) use ($roleMap) {
                $authorName = 'Unknown User';
                $authorAvatar = '/assets/DEFAULT.jpg';
                $authorRole = 'Resident';

                if ($post->author) {
                    $authorName = $post->author->name ?? 'Unknown User';
                    if ($post->author->profile_pic) {
                        $authorAvatar = $post->author->profile_pic;
                        if (!str_starts_with($authorAvatar, 'http') && !str_starts_with($authorAvatar, '/storage/')) {
                            $authorAvatar = '/storage/' . $authorAvatar;
                        }
                    } else {
                        $authorAvatar = $post->author->avatar ?? '/assets/DEFAULT.jpg';
                    }
                    $authorRole = $roleMap[$post->author->fk_role_id ?? 1] ?? 'Resident';
                }

                $createdAt = $post->created_at ? Carbon::parse($post->created_at) : now();

                $images = [];
                if (!empty($post->image_content)) {
                    $images[] = asset('storage/' . $post->image_content);
                }

                $tags = $post->tags && $post->tags->count() ? $post->tags->pluck('tag_name')->toArray() : [];

                $postHeader = null;
                if (Schema::hasColumn('posts', 'header')) {
                    try {
                        $headerValue = $post->header ?? null;
                        $postHeader = (!empty(trim($headerValue ?? ''))) ? trim($headerValue) : null;
                    } catch (\Exception $e) {
                        $postHeader = null;
                    }
                }

                return [
                    'id' => $post->post_id,
                    'author' => $authorName,
                    'avatar' => $authorAvatar,
                    'role' => $authorRole,
                    'header' => $postHeader,
                    'content' => $post->content ?? '',
                    'images' => $images,
                    'tags' => $tags,
                    'date' => $createdAt->format('M d, Y'),
                    'time' => $createdAt->format('g:i A'),
                    'likes' => DB::table('post_reactions')
                        ->where('fk_post_id', $post->post_id)
                        ->where('reaction_type', 'Like')
                        ->count(),
                    'comments' => DB::table('post_comments')
                        ->where('fk_post_id', $post->post_id)
                        ->count(),
                ];
            });

        return Inertia::render('Welcome', [
            'announcements' => $announcements->values()->toArray(),
            'discussions' => $discussions->values()->toArray(),
        ]);
    }
}

