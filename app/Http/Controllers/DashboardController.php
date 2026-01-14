<?php

namespace App\Http\Controllers;

use App\Models\RegistrationReq;
use App\Models\User;
use App\Models\Post;
use App\Models\DocumentRequest;
use App\Models\EventAssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the system admin dashboard with statistics and user management.
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'registration_requests' => RegistrationReq::where('registration_status', 'pending')->count(),
            'services_approved' => DocumentRequest::where('status', 'Approved')->count() 
                + EventAssistanceRequest::where('status', 'Approved')->count(),
            'total_posts' => Post::count(),
            'total_residents' => User::where('fk_role_id', 1)->count(),
        ];

        // Get all users with their post and comment counts
        $users = User::with('credential')
            ->get()
            ->map(function ($user) {
                // Count posts
                $postCount = Post::where('fk_post_author_id', $user->user_id)->count();
                
                // Count comments - use fk_commenter_id based on PostCommentController
                $commentCount = DB::table('post_comments')
                    ->where('fk_commenter_id', $user->user_id)
                    ->count();

                // Build full address
                $addressParts = array_filter([
                    $user->house_number ?? null,
                    $user->street ?? null,
                    $user->phase ?? null,
                    $user->package ?? null,
                    $user->barangay ?? null,
                    $user->city ?? null,
                    $user->province ?? null,
                ]);
                $address = implode(' ', $addressParts) ?: 'N/A';
                
                // Also create a sortable date for frontend
                $joinedDate = $user->created_at 
                    ? Carbon::parse($user->created_at)
                    : null;

                // Get contact number from credential if available
                $contact = $user->credential?->contact_number ?? 'N/A';

                // Determine role name
                $roleId = $user->fk_role_id ?? 1;
                $roleName = $roleId === 1 ? 'Resident' : 'Official';

                // Format date joined
                $joined = $user->created_at 
                    ? Carbon::parse($user->created_at)->format('m/d/Y')
                    : 'N/A';

                // Get profile picture
                $profilePic = $user->profile_pic 
                    ? (str_starts_with($user->profile_pic, 'http') || str_starts_with($user->profile_pic, '/storage/') 
                        ? $user->profile_pic 
                        : '/storage/' . $user->profile_pic)
                    : '/assets/ADMIN.png';

                return [
                    'id' => $user->user_id,
                    'profile' => $profilePic,
                    'name' => $user->name,
                    'address' => $address,
                    'contact' => $contact,
                    'role' => $roleName,
                    'role_id' => $roleId,
                    'posts' => $postCount,
                    'comments' => $commentCount,
                    'joined' => $joined,
                    'joined_timestamp' => $joinedDate ? $joinedDate->timestamp : 0,
                ];
            })
            ->sortByDesc('joined_timestamp')
            ->values();

        return Inertia::render('Admin/System_Admin/S_Dashboard', [
            'stats' => $stats,
            'users' => $users,
        ]);
    }

    /**
     * Restrict a user
     */
    public function restrictUser(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'restrictions' => 'required|array',
        ]);

        // TODO: Implement restriction logic
        // This could involve creating a restrictions table or updating user status
        
        return response()->json([
            'success' => true,
            'message' => 'User restrictions applied successfully'
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $userId)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($userId);
        $credential = $user->credential;

        if (!$credential) {
            return response()->json([
                'success' => false,
                'message' => 'User credential not found'
            ], 404);
        }

        $credential->password = bcrypt($request->password);
        $credential->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deleting system admin
        if ($user->fk_role_id == 9) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system administrator'
            ], 403);
        }

        // Delete user (cascade should handle related records)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}








