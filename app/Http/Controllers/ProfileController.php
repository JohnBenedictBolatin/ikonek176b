<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoleCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('roleCategory');

        $roleCategories = RoleCategory::select('role_id as id', 'role_name as name')->orderBy('role_name')->get();

        // Get accurate counts from database
        $userId = $user->user_id;
        
        // Count posts
        $postsCount = DB::table('posts')
            ->where('fk_post_author_id', $userId)
            ->count();
        
        // Count reactions (likes + dislikes) received on user's posts
        $reactionsCount = DB::table('post_reactions')
            ->join('posts', 'post_reactions.fk_post_id', '=', 'posts.post_id')
            ->where('posts.fk_post_author_id', $userId)
            ->count();
        
        // Count comments received on user's posts
        $commentsCount = DB::table('post_comments')
            ->join('posts', 'post_comments.fk_post_id', '=', 'posts.post_id')
            ->where('posts.fk_post_author_id', $userId)
            ->count();
        
        // Get user's posts with tags, reactions, and comments
        $userPosts = DB::table('posts')
            ->where('fk_post_author_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($post) use ($user) {
                // Get tags for this post
                $tags = DB::table('post_tags')
                    ->join('tags', 'post_tags.fk_tag_id', '=', 'tags.tag_id')
                    ->where('post_tags.fk_post_id', $post->post_id)
                    ->pluck('tags.tag_name')
                    ->toArray();
                
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
                
                // Format date - DB::table returns strings, not Carbon instances
                $createdAt = $post->created_at ? \Carbon\Carbon::parse($post->created_at) : now();
                
                return [
                    'post_id' => $post->post_id,
                    'id' => $post->post_id,
                    'content' => $post->content ?? '',
                    'image_content' => $post->image_content ?? null,
                    'created_at' => $post->created_at,
                    'date' => $createdAt->toISOString(),
                    'time' => $createdAt->format('g:i A'),
                    'author' => $user->name ?? 'Unknown',
                    'avatar' => $user->profile_pic ? (str_starts_with($user->profile_pic, '/storage/') ? $user->profile_pic : '/storage/' . $user->profile_pic) : '/assets/DEFAULT.jpg',
                    'tags' => $tags,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'comments' => $comments,
                ];
            });

        // Load user credential for contact info
        $user->load('credential');
        
        // Prepare user info for display
        $birthdateFormatted = null;
        if ($user->birthdate) {
            if ($user->birthdate instanceof Carbon) {
                $birthdateFormatted = $user->birthdate->format('F d, Y');
            } elseif (is_string($user->birthdate)) {
                try {
                    $birthdateFormatted = Carbon::parse($user->birthdate)->format('F d, Y');
                } catch (\Exception $e) {
                    $birthdateFormatted = $user->birthdate;
                }
            }
        }
        
        $userInfo = [
            'email' => $user->credential->email ?? null,
            'contact_number' => $user->credential->contact_number ?? null,
            'secondary_contact_number' => $user->credential->secondary_contact_number ?? null,
            'address' => $this->formatAddress($user),
            'phase' => $user->phase ?? null,
            'package' => $user->package ?? null,
            'barangay' => $user->barangay ?? null,
            'birthdate' => $birthdateFormatted,
            'sex' => $user->sex ?? null,
            'civil_status' => $user->civil_status ?? null,
        ];

        // Render the exact component path used in your Route::inertia
        return Inertia::render('User/Resident/R_Profile', [
            'roleCategories' => $roleCategories,
            'stats' => [
                'posts' => $postsCount,
                'reactions' => $reactionsCount,
                'comments' => $commentsCount,
            ],
            'userPosts' => $userPosts->values()->all(), // Convert collection to array
            'userInfo' => $userInfo, // Pass additional user info
            // don't pass top-level 'user' — rely on shared auth.user
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'profile_description' => ['nullable', 'string', 'max:2000'],
            'fk_role_id' => ['nullable', 'integer', 'exists:role_categories,role_id'],
        ]);

        $user->profile_description = $data['profile_description'] ?? $user->profile_description;
        if (array_key_exists('fk_role_id', $data) && $data['fk_role_id'] !== null) {
            $user->fk_role_id = $data['fk_role_id'];
        }
        $user->save();

        // Redirect to the canonical profile page so the next Inertia response includes updated auth.user
        return redirect()->route('profile.edit')->with('message', 'Profile updated successfully.');
    }

    public function employeeProfile()
    {
        $user = Auth::user();
        
        // Get accurate counts from database
        $userId = $user->user_id;
        
        // Count posts
        $postsCount = DB::table('posts')
            ->where('fk_post_author_id', $userId)
            ->count();
        
        // Count reactions (likes + dislikes) received on user's posts
        $reactionsCount = DB::table('post_reactions')
            ->join('posts', 'post_reactions.fk_post_id', '=', 'posts.post_id')
            ->where('posts.fk_post_author_id', $userId)
            ->count();
        
        // Count comments received on user's posts
        $commentsCount = DB::table('post_comments')
            ->join('posts', 'post_comments.fk_post_id', '=', 'posts.post_id')
            ->where('posts.fk_post_author_id', $userId)
            ->count();
        
        // Get user's posts with tags, reactions, and comments
        $userPosts = DB::table('posts')
            ->where('fk_post_author_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($post) use ($user) {
                // Get tags for this post
                $tags = DB::table('post_tags')
                    ->join('tags', 'post_tags.fk_tag_id', '=', 'tags.tag_id')
                    ->where('post_tags.fk_post_id', $post->post_id)
                    ->pluck('tags.tag_name')
                    ->toArray();
                
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
                
                // Format date - DB::table returns strings, not Carbon instances
                $createdAt = $post->created_at ? \Carbon\Carbon::parse($post->created_at) : now();
                
                return [
                    'post_id' => $post->post_id,
                    'id' => $post->post_id,
                    'content' => $post->content ?? '',
                    'image_content' => $post->image_content ?? null,
                    'created_at' => $post->created_at,
                    'date' => $createdAt->toISOString(),
                    'time' => $createdAt->format('g:i A'),
                    'author' => $user->name ?? 'Unknown',
                    'avatar' => $user->profile_pic ? (str_starts_with($user->profile_pic, '/storage/') ? $user->profile_pic : '/storage/' . $user->profile_pic) : '/assets/DEFAULT.jpg',
                    'tags' => $tags,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'comments' => $comments,
                ];
            });
        
        return Inertia::render('User/Employee/E_Profile', [
            'stats' => [
                'posts' => $postsCount,
                'reactions' => $reactionsCount,
                'comments' => $commentsCount,
            ],
            'userPosts' => $userPosts->values()->all(), // Convert collection to array
        ]);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();
        
        // Delete old profile picture if exists
        if ($user->profile_pic) {
            $oldPath = $user->profile_pic;
            // Remove /storage/ prefix if present
            if (strpos($oldPath, 'storage/') === 0) {
                $oldPath = substr($oldPath, 8);
            }
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        
        // Update user's profile_pic
        $user->profile_pic = $path;
        $user->save();
        
        return back()->with('success', 'Profile picture updated successfully.');
    }

    /**
     * Format user address from multiple fields (only phase and package)
     */
    private function formatAddress($user)
    {
        $parts = [];
        if ($user->package) {
            // Remove "Package" prefix if already present
            $package = trim($user->package);
            if (stripos($package, 'Package') === 0) {
                $package = trim(substr($package, 7)); // Remove "Package" prefix
            }
            $parts[] = 'Package ' . $package;
        }
        if ($user->phase) {
            // Remove "Phase" prefix if already present
            $phase = trim($user->phase);
            if (stripos($phase, 'Phase') === 0) {
                $phase = trim(substr($phase, 5)); // Remove "Phase" prefix
            }
            $parts[] = 'Phase ' . $phase;
        }
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

}
