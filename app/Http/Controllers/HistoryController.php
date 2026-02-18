<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostReport;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Get registered officials (users with fk_role_id != 1)
     */
    public function getOfficials()
    {
        try {
            // Get users who are officials (role_id != 1) and were created by system admin
            // Officials are typically created directly, not through registration requests
            $officials = User::where('fk_role_id', '!=', 1)
                ->whereNotNull('fk_role_id')
                ->whereIn('fk_role_id', [2, 3, 4, 5, 6, 7, 9]) // Official roles
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->user_id,
                        'user_id' => $user->user_id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                        'fk_role_id' => $user->fk_role_id,
                        'role_id' => $user->fk_role_id,
                        'profile_pic' => $user->profile_pic ?? null,
                        'profile_image' => $user->profile_pic ?? null,
                        'created_at' => $user->created_at?->toDateTimeString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'officials' => $officials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching officials: ' . $e->getMessage(),
                'officials' => []
            ], 500);
        }
    }

    /**
     * Get reviewed reports (dismissed, deleted, removed, reviewed)
     */
    public function getReports()
    {
        try {
            // Get reports that have been reviewed (not pending)
            // ReportController sets status to 'Reviewed' when dismissing, and 'Reviewed' when deleting posts
            $reports = PostReport::whereIn('status', ['Reviewed', 'reviewed', 'Dismissed', 'Deleted', 'Removed', 'dismissed', 'deleted', 'removed'])
                ->with(['reporter:id,first_name,last_name,profile_pic', 'post:id,post_header'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($report) {
                    $reporter = $report->reporter;
                    
                    // Determine action based on status and whether post still exists
                    $status = strtolower($report->status ?? '');
                    $action = 'Reviewed';
                    if (in_array($status, ['deleted', 'removed']) || ($status === 'reviewed' && !$report->post)) {
                        $action = 'Deleted';
                    } elseif (in_array($status, ['dismissed']) || ($status === 'reviewed' && $report->post)) {
                        $action = 'Dismissed';
                    }
                    
                    return [
                        'id' => $report->post_report_id ?? $report->id,
                        'post_report_id' => $report->post_report_id ?? $report->id,
                        'reported_by' => $reporter ? trim(($reporter->first_name ?? '') . ' ' . ($reporter->last_name ?? '')) : 'Unknown',
                        'reporter_name' => $reporter ? trim(($reporter->first_name ?? '') . ' ' . ($reporter->last_name ?? '')) : 'Unknown',
                        'reporter_avatar' => $reporter->profile_pic ?? null,
                        'reporter_profile_pic' => $reporter->profile_pic ?? null,
                        'status' => $action, // Use the determined action
                        'reviewed_at' => $report->updated_at?->toDateTimeString(),
                        'updated_at' => $report->updated_at?->toDateTimeString(),
                        'created_at' => $report->created_at?->toDateTimeString(),
                        'reviewed_by' => Auth::user()?->name ?? 'System Admin',
                    ];
                });

            return response()->json([
                'success' => true,
                'reports' => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching reports: ' . $e->getMessage(),
                'reports' => []
            ], 500);
        }
    }

    /**
     * Get reviewed contact messages (Read, Replied)
     */
    public function getMessages()
    {
        try {
            // Get contact messages that have been reviewed (Read or Replied)
            $messages = ContactMessage::whereIn('status', ['Read', 'Replied', 'read', 'replied'])
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($message) {
                    // Get user profile pic if user_id exists
                    $profilePic = null;
                    if ($message->user_id && $message->user) {
                        $profilePic = $message->user->profile_pic ?? null;
                    }
                    
                    return [
                        'id' => $message->id,
                        'contact_id' => $message->id,
                        'user_name' => $message->user_name ?? 'Guest',
                        'first_name' => $message->first_name ?? null,
                        'last_name' => $message->last_name ?? null,
                        'user_type' => $message->user_type ?? 'Guest',
                        'user_avatar' => $profilePic,
                        'profile_pic' => $profilePic,
                        'status' => ucfirst(strtolower($message->status)),
                        'reviewed_at' => $message->updated_at?->toDateTimeString(),
                        'updated_at' => $message->updated_at?->toDateTimeString(),
                        'created_at' => $message->created_at?->toDateTimeString(),
                        'reviewed_by' => Auth::user()?->name ?? 'System Admin',
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching messages: ' . $e->getMessage(),
                'messages' => []
            ], 500);
        }
    }
}

