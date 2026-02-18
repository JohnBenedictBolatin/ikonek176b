<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostReport;
use App\Models\PostReportReason;
use App\Models\Post;
use App\Models\User;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Store a new report
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'post_id' => ['required', 'integer', 'exists:posts,post_id'],
                'reasons' => ['required', 'array', 'min:1'],
                'reasons.*' => ['required', 'string', 'in:spam,harassment,hate,violence,inappropriate,false,other'],
                'details' => ['nullable', 'string', 'max:500'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in report submission', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            // Get the post to check ownership
            $post = Post::find($validated['post_id']);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            // Check if user is trying to report their own post
            $userId = Auth::id();
            $postAuthorId = $post->fk_post_author_id ?? $post->user_id ?? null;
            
            if ($userId == $postAuthorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot report your own post.',
                ], 403);
            }

            // Check if user already reported this post
            // Check both 'Pending' and 'pending' to be safe
            $existingReport = PostReport::where('fk_post_id', $validated['post_id'])
                ->where('fk_reporter_id', Auth::id())
                ->whereIn('status', ['Pending', 'pending'])
                ->first();

            if ($existingReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reported this post.',
                ], 400);
            }

            $report = new PostReport();
            $report->fk_post_id = $validated['post_id'];
            $report->fk_reporter_id = Auth::id();
            $report->reason = null; // Keep for backward compatibility but not used
            $report->details = $validated['details'] ?? null;
            // Use 'Pending' to match database enum (capitalized)
            $report->status = 'Pending';
            $report->save();

            // Save multiple reasons
            foreach ($validated['reasons'] as $reason) {
                $reportReason = new PostReportReason();
                $reportReason->fk_report_id = $report->post_report_id ?? $report->getKey();
                $reportReason->reason = $reason;
                $reportReason->save();
            }

            // Mark post as reported (post is already loaded above)
            if ($post) {
                $post->is_reported = true;
                $post->save();
            }

            Log::info('Report created successfully', ['report_id' => $report->post_report_id ?? $report->getKey()]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully. Thank you for helping keep our community safe.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error creating report: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
            ]);
            
            // Check if table doesn't exist
            if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), 'Base table or view not found') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database table not found. Please run migrations: php artisan migrate',
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error creating report: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error submitting report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all reports for admin
     */
    public function index()
    {
        try {
            // Check both 'Pending' and 'pending' to be safe
            $reports = PostReport::with(['post.author', 'reporter', 'reasons'])
                ->whereIn('status', ['Pending', 'pending'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($report) {
                    $post = $report->post;
                    $postAuthor = $post->author ?? null;
                    $reporter = $report->reporter ?? null;

                    // Get role name
                    $roleMap = [
                        1 => 'Resident',
                        2 => 'Barangay Captain',
                        3 => 'Barangay Secretary',
                        4 => 'Barangay Treasurer',
                        5 => 'Barangay Kagawad',
                        6 => 'SK Chairman',
                        7 => 'Sangguniang Kabataan Kagawad',
                        9 => 'System Admin',
                    ];

                    $postAuthorRole = $postAuthor ? ($roleMap[$postAuthor->fk_role_id ?? $postAuthor->role_id ?? 1] ?? 'Resident') : 'Unknown';
                    $reporterRole = $reporter ? ($roleMap[$reporter->fk_role_id ?? $reporter->role_id ?? 1] ?? 'Resident') : 'Unknown';

                    // Format reasons
                    $reasonMap = [
                        'spam' => 'Spam or misleading',
                        'harassment' => 'Harassment or bullying',
                        'hate' => 'Hate speech',
                        'violence' => 'Violence or dangerous content',
                        'inappropriate' => 'Inappropriate content',
                        'false' => 'False information',
                        'other' => 'Other',
                    ];
                    
                    // Get all reasons from the relationship, fallback to old reason field for backward compatibility
                    $reportReasons = $report->reasons->pluck('reason')->toArray();
                    if (empty($reportReasons) && $report->reason) {
                        $reportReasons = [$report->reason];
                    }
                    
                    $reasonLabels = array_map(function($reason) use ($reasonMap) {
                        return $reasonMap[$reason] ?? $reason;
                    }, $reportReasons);

                    // Format avatar paths
                    $postAuthorAvatar = '/assets/DEFAULT.jpg';
                    if ($postAuthor && $postAuthor->profile_pic) {
                        $postAuthorAvatar = str_starts_with($postAuthor->profile_pic, '/storage/') 
                            ? $postAuthor->profile_pic 
                            : '/storage/' . $postAuthor->profile_pic;
                    }
                    
                    $reporterAvatar = '/assets/DEFAULT.jpg';
                    if ($reporter && $reporter->profile_pic) {
                        $reporterAvatar = str_starts_with($reporter->profile_pic, '/storage/') 
                            ? $reporter->profile_pic 
                            : '/storage/' . $reporter->profile_pic;
                    }

                    // Get report ID - use the primary key
                    $reportId = $report->post_report_id ?? $report->getKey();
                    
                    // Format image path
                    $postImage = null;
                    if ($post->image_content) {
                        $postImage = str_starts_with($post->image_content, 'http') 
                            ? $post->image_content 
                            : asset('storage/' . $post->image_content);
                    }
                    
                    return [
                        'id' => $reportId,
                        'report_id' => $reportId,
                        'post_report_id' => $reportId,
                        'post_id' => $post->post_id,
                        'postAuthor' => $postAuthor->name ?? 'Unknown',
                        'postAuthorId' => $postAuthor->user_id ?? null,
                        'postAuthorRole' => $postAuthorRole,
                        'postAuthorAvatar' => $postAuthorAvatar,
                        'postAuthorIsRestricted' => $postAuthor ? $this->isUserRestricted($postAuthor->user_id) : false,
                        'postAuthorRestrictions' => $postAuthor ? $this->getUserRestrictions($postAuthor->user_id) : null,
                        'postTitle' => $post->header ?? 'No Title',
                        'postContent' => $post->content ?? '',
                        'postImage' => $postImage,
                        'postDate' => $post->created_at ? $post->created_at->format('m/d/Y') : 'N/A',
                        'reportedBy' => $reporter->name ?? 'Unknown',
                        'reportedByAvatar' => $reporterAvatar,
                        'reporterRole' => $reporterRole,
                        'reportReasons' => $reasonLabels,
                        'reportReason' => implode(', ', $reasonLabels), // For backward compatibility
                        'reportDetails' => (in_array('other', $reportReasons) && !empty(trim($report->details ?? '')) && strtolower(trim($report->details)) !== 'no additional details provided.') 
                            ? trim($report->details) 
                            : null,
                        'reportDate' => $report->created_at ? $report->created_at->format('m/d/Y H:i') : 'N/A',
                        'reportDateTime' => $report->created_at ? $report->created_at->toISOString() : null,
                        'dateObj' => $report->created_at, // For sorting by report date
                    ];
                });

            // Get contact messages with status 'New'
            $contactMessages = ContactMessage::where('status', 'New')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => 'contact_' . $message->id,
                        'type' => 'contact',
                        'contact_id' => $message->id,
                        'user_name' => $message->user_name ?? 'Guest',
                        'first_name' => $message->first_name,
                        'last_name' => $message->last_name,
                        'user_email' => $message->user_email,
                        'user_contact_number' => $message->user_contact_number,
                        'user_type' => $message->user_type ?? 'Guest',
                        'message' => $message->message,
                        'reportDate' => $message->created_at ? $message->created_at->format('m/d/Y H:i') : 'N/A',
                        'reportDateTime' => $message->created_at ? $message->created_at->toISOString() : null,
                        'dateObj' => $message->created_at,
                    ];
                });

            // Combine reports and contact messages, sort by date
            $allReports = $reports->concat($contactMessages)->sortByDesc(function ($item) {
                return $item['dateObj'] ?? now();
            })->values();

            return Inertia::render('Admin/System_Admin/S_Report', [
                'reports' => $allReports,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching reports: ' . $e->getMessage());
            return Inertia::render('Admin/System_Admin/S_Report', [
                'reports' => [],
                'error' => 'Error loading reports.',
            ]);
        }
    }

    /**
     * Dismiss a report (keep the post)
     */
    public function dismiss(Request $request, $id)
    {
        try {
            $report = PostReport::findOrFail($id);
            
            Log::info('Attempting to dismiss report', [
                'post_report_id' => $id,
                'current_status' => $report->status,
                'report_data' => $report->toArray()
            ]);
            
            // Based on database check, status enum appears to be 'Pending'/'Reviewed'
            // Try 'Reviewed' first (as that's what the database shows)
            $newStatus = 'Reviewed';
            
            // Try to set the status
            $report->status = $newStatus;
            $report->save();
            
            Log::info('Report status updated', [
                'post_report_id' => $id,
                'new_status' => $report->status
            ]);

            // Check if there are any other pending reports for this post
            // Check both 'Pending' and 'pending' to be safe
            $pendingReports = PostReport::where('fk_post_id', $report->fk_post_id)
                ->whereIn('status', ['Pending', 'pending'])
                ->count();

            // If no pending reports, unmark post as reported
            if ($pendingReports === 0) {
                $post = Post::find($report->fk_post_id);
                if ($post) {
                    $post->is_reported = false;
                    $post->save();
                }
            }

            Log::info('Report dismissed successfully', ['post_report_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Report dismissed successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error dismissing report', [
                'post_report_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error dismissing report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a post and resolve all its reports
     */
    public function deletePost(Request $request, $postId)
    {
        try {
            Log::info('Attempting to delete post', ['post_id' => $postId]);
            
            DB::transaction(function () use ($postId) {
                // Get the post and author before deletion
                $post = Post::findOrFail($postId);
                $postAuthorId = $post->fk_post_author_id ?? $post->user_id ?? null;
                
                // Get all reports for this post to collect reasons
                $reports = PostReport::where('fk_post_id', $postId)
                    ->with('reasons')
                    ->get();
                
                // Collect all unique report reasons
                $allReasons = [];
                foreach ($reports as $report) {
                    $reportReasons = $report->reasons->pluck('reason')->toArray();
                    if (empty($reportReasons) && $report->reason) {
                        $reportReasons = [$report->reason];
                    }
                    $allReasons = array_merge($allReasons, $reportReasons);
                }
                $uniqueReasons = array_unique($allReasons);
                
                // Map reason codes to human-readable labels
                $reasonMap = [
                    'spam' => 'Spam or misleading content',
                    'harassment' => 'Harassment or bullying',
                    'hate' => 'Hate speech',
                    'violence' => 'Violence or dangerous content',
                    'inappropriate' => 'Inappropriate content',
                    'false' => 'False information',
                    'other' => 'Other violation',
                ];
                
                $reasonLabels = array_map(function($reason) use ($reasonMap) {
                    return $reasonMap[$reason] ?? $reason;
                }, $uniqueReasons);
                
                // Build notification message
                $reasonText = !empty($reasonLabels) 
                    ? implode(', ', $reasonLabels)
                    : 'Community guidelines violation';
                
                $notificationMessage = "Your post has been removed by the system administrator due to the following reason(s): {$reasonText}. Please review our community guidelines to ensure your future posts comply with our standards.";
                
                // Update all reports for this post to Reviewed
                PostReport::where('fk_post_id', $postId)
                    ->update(['status' => 'Reviewed']);
                
                Log::info('Reports updated to Reviewed', ['post_id' => $postId]);

                // Create notification for post author if author exists
                if ($postAuthorId) {
                    try {
                        // Check if notification table uses fk_user_id or user_id
                        $userIdColumn = Schema::hasColumn('notifications', 'fk_user_id') ? 'fk_user_id' : 'user_id';
                        $typeColumn = Schema::hasColumn('notifications', 'notification_type') ? 'notification_type' : 'type';
                        $refIdColumn = Schema::hasColumn('notifications', 'notification_reference_id') ? 'notification_reference_id' : 'reference_id';
                        
                        DB::table('notifications')->insert([
                            $userIdColumn => $postAuthorId,
                            $typeColumn => 'Report',
                            $refIdColumn => $postId,
                            'message' => $notificationMessage,
                            'is_read' => false,
                            'created_at' => now(),
                        ]);
                        
                        Log::info('Notification created for post author', [
                            'post_id' => $postId,
                            'author_id' => $postAuthorId,
                            'reasons' => $reasonLabels
                        ]);
                    } catch (\Exception $notifError) {
                        // Log but don't fail the deletion if notification fails
                        Log::error('Error creating notification for deleted post', [
                            'post_id' => $postId,
                            'author_id' => $postAuthorId,
                            'error' => $notifError->getMessage()
                        ]);
                    }
                }
                
                // Delete associated image if exists
                if ($post->image_content) {
                    Storage::disk('public')->delete($post->image_content);
                }

                $post->delete();
                
                Log::info('Post deleted successfully', ['post_id' => $postId]);
            });

            Log::info('Post deleted by admin', ['post_id' => $postId]);

            return response()->json([
                'success' => true,
                'message' => 'Post removed permanently.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting post', [
                'post_id' => $postId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark contact message as read/replied
     */
    public function updateContactMessage(Request $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $status = $request->input('status', 'Read');
            
            if (!in_array($status, ['New', 'Read', 'Replied'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status.',
                ], 400);
            }

            $message->status = $status;
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Contact message updated successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating contact message', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating contact message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if a user is restricted
     */
    private function isUserRestricted($userId)
    {
        if (!$userId) {
            return false;
        }

        $userRestriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
        
        if (!$userRestriction) {
            return false;
        }

        return $userRestriction->restrict_posting ||
               $userRestriction->restrict_commenting ||
               $userRestriction->restrict_document_request ||
               $userRestriction->restrict_event_assistance_request;
    }

    /**
     * Get user restrictions
     */
    private function getUserRestrictions($userId)
    {
        if (!$userId) {
            return null;
        }

        $userRestriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
        
        if (!$userRestriction) {
            return null;
        }

        return [
            'posting' => (bool) $userRestriction->restrict_posting,
            'commenting' => (bool) $userRestriction->restrict_commenting,
            'documentRequest' => (bool) $userRestriction->restrict_document_request,
            'eventAssistanceRequest' => (bool) $userRestriction->restrict_event_assistance_request,
        ];
    }
}

