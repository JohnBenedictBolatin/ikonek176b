<?php

namespace App\Services;

use App\Models\Post;
use App\Models\UserRestriction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AntiSpamService
{
    /**
     * Check if user has posted duplicate content and handle spam detection
     * 
     * @param int $userId
     * @param string $content
     * @param string|null $header
     * @return array ['is_spam' => bool, 'duplicate_count' => int, 'action_taken' => string]
     */
    public static function checkAndHandleSpam(int $userId, string $content, ?string $header = null): array
    {
        // Normalize content for comparison (remove extra whitespace, convert to lowercase)
        $normalizedContent = self::normalizeContent($content);
        $normalizedHeader = $header ? self::normalizeContent($header) : null;
        
        // Get recent posts by this user (last 24 hours OR last 10 posts, whichever gives more results)
        // This prevents false positives from old duplicate posts while still catching rapid spam
        $timeWindow = now()->subHours(24);
        
        // Get posts from last 24 hours
        $recentPosts = Post::where('fk_post_author_id', $userId)
            ->where('created_at', '>=', $timeWindow)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get last 10 posts regardless of time
        $lastTenPosts = Post::where('fk_post_author_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Merge and deduplicate (use the collection that has more posts, or merge both)
        $userPosts = $recentPosts->merge($lastTenPosts)->unique('post_id')->sortByDesc('created_at');
        
        $duplicatePosts = [];
        
        foreach ($userPosts as $post) {
            $postContent = self::normalizeContent($post->content ?? '');
            $postHeader = $post->header ? self::normalizeContent($post->header) : null;
            
            // Check if content matches (exact match or very similar)
            $contentMatches = $postContent === $normalizedContent;
            
            // Also check if header matches if both exist
            $headerMatches = false;
            if ($normalizedHeader && $postHeader) {
                $headerMatches = $postHeader === $normalizedHeader;
            }
            
            // Consider it a duplicate if content matches exactly
            // or if both header and content match (for posts with headers)
            if ($contentMatches || ($headerMatches && $contentMatches)) {
                $duplicatePosts[] = $post;
            }
        }
        
        $duplicateCount = count($duplicatePosts);
        
        // If user has 3 or more duplicate posts (including the current one being posted)
        // The current post hasn't been saved yet, so we check if we have 2+ existing duplicates
        if ($duplicateCount >= 2) {
            // This will be the 3rd duplicate post
            Log::warning('🚫 Spam detected: User has posted duplicate content', [
                'user_id' => $userId,
                'duplicate_count' => $duplicateCount + 1, // +1 for the current post being created
                'content_preview' => substr($content, 0, 50)
            ]);
            
            // Delete all duplicate posts
            $deletedCount = 0;
            foreach ($duplicatePosts as $post) {
                try {
                    // Delete associated poll if exists
                    if ($post->poll) {
                        $post->poll->delete();
                    }
                    
                    // Delete associated tags
                    $post->tags()->detach();
                    
                    // Delete the post
                    $post->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    Log::error('Error deleting duplicate post: ' . $e->getMessage(), [
                        'post_id' => $post->post_id,
                        'user_id' => $userId
                    ]);
                }
            }
            
            // Restrict user from posting
            self::restrictUser($userId);
            
            // Create notification
            self::createRestrictionNotification($userId, $deletedCount);
            
            return [
                'is_spam' => true,
                'duplicate_count' => $duplicateCount + 1,
                'action_taken' => 'deleted_and_restricted',
                'deleted_posts' => $deletedCount
            ];
        }
        
        return [
            'is_spam' => false,
            'duplicate_count' => $duplicateCount,
            'action_taken' => 'none'
        ];
    }
    
    /**
     * Normalize content for comparison
     * 
     * @param string $content
     * @return string
     */
    private static function normalizeContent(string $content): string
    {
        // Convert to lowercase
        $normalized = mb_strtolower($content);
        
        // Remove all punctuation and special characters (except spaces)
        // This prevents bypassing with punctuation variations
        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', '', $normalized);
        
        // Remove extra whitespace (multiple spaces, tabs, newlines)
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        // Trim whitespace
        $normalized = trim($normalized);
        
        return $normalized;
    }
    
    /**
     * Restrict user from posting
     * 
     * @param int $userId
     * @return void
     */
    private static function restrictUser(int $userId): void
    {
        try {
            $restriction = UserRestriction::where('user_id', $userId)->first();
            
            if ($restriction) {
                // Update existing restriction
                $restriction->restrict_posting = true;
                $restriction->restriction_reason = 'Spam detected: Multiple duplicate posts detected. Your posting privileges have been restricted.';
                $restriction->restricted_by = null; // System restriction
                $restriction->save();
            } else {
                // Create new restriction
                UserRestriction::create([
                    'user_id' => $userId,
                    'restrict_posting' => true,
                    'restrict_commenting' => false,
                    'restrict_document_request' => false,
                    'restrict_event_assistance_request' => false,
                    'restriction_reason' => 'Spam detected: Multiple duplicate posts detected. Your posting privileges have been restricted.',
                    'restricted_by' => null, // System restriction
                ]);
            }
            
            Log::info('✅ User restricted from posting due to spam', [
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error restricting user: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);
        }
    }
    
    /**
     * Create notification for user about restriction
     * 
     * @param int $userId
     * @param int $deletedPostsCount
     * @return void
     */
    private static function createRestrictionNotification(int $userId, int $deletedPostsCount): void
    {
        try {
            $message = "Your posting privileges have been restricted due to spam detection. {$deletedPostsCount} duplicate post(s) have been removed. Please contact the barangay administration if you believe this is an error.";
            
            // Check notification table structure - it might use different column names
            // Based on the migration, it uses: user_id, message, type, reference_id, reference_table, is_read, created_at
            // But the DocumentRequestController uses: fk_user_id, notification_type, notification_reference_id
            // Let me check which structure is correct by looking at the actual usage
            
            // Use the structure consistent with other controllers
            DB::table('notifications')->insert([
                'fk_user_id' => $userId,
                'message' => $message,
                'notification_type' => 'Report', // Using Report type for restrictions (as seen in DashboardController)
                'notification_reference_id' => $userId,
                'is_read' => false,
                'created_at' => now(),
            ]);
            
            Log::info('✅ Restriction notification created', [
                'user_id' => $userId,
                'deleted_posts' => $deletedPostsCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating restriction notification: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);
        }
    }
}

