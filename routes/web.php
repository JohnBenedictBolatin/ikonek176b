<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\NotificationRequestController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\RegisterRequestController;
use Illuminate\Support\Facades\DB;  
use App\Http\Controllers\RegisterResidentController;
use App\Http\Controllers\RegisterOfficialController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventAssistanceRequestController;
use App\Http\Controllers\EventAssistanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EmployeeDiscussionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentAnnouncementController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WelcomeController;

Route::get('/calendar-static', function () {
    return Inertia::render('CalendarStatic');
})->name('calendar.static');
Route::get('/check-db', function () {
    if (DB::connection()->getDatabaseName()) {
        return "Connected to database: " . DB::connection()->getDatabaseName();
    } else {
        return "Not connected to any database.";
    }
});

Route::get('/fix-post-header-column', function () {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('posts', 'post_header')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE posts ADD COLUMN post_header VARCHAR(255) NULL AFTER fk_post_author_id");
            return "✅ Column 'post_header' has been added successfully!";
        } else {
            $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM posts WHERE Field = 'post_header'");
            return "✅ Column already exists. Definition: " . json_encode($columns[0]);
        }
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            return "✅ Column already exists (duplicate error caught)";
        }
        return "❌ Error: " . $e->getMessage();
    }
});

// routes/web.php (temporary)
Route::get('/phpinfo-debug', function () {
    return response()->json([
        'loaded_ini' => php_ini_loaded_file(), // the exact ini file PHP loaded
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'upload_tmp_dir' => ini_get('upload_tmp_dir'),
    ]);
});



Route::get("/", [WelcomeController::class, 'index'])->name('welcome');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

Route::inertia("/login_user", 'Login_User')->name('login');
Route::post('/login/check', [LoginController::class, 'check'])->name('login.check');

// Unified logout route for all users (residents, employees, admins)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect residents/employees to user login
    return redirect()->route('login');
})->name('logout');

Route::inertia("/login_employee", 'Login_employee')->name('login_employee');

Route::inertia("/login_moderator", 'Admin/Login_Moderator')->name('login_moderator');

Route::inertia("/login_approver", 'Admin/Login_Approver')->name('login_approver');

Route::inertia("/login_treasurer", 'Admin/Login_Treasurer')->name('login_treasurer');

Route::inertia("/login_admin", 'Admin/Login_Admin')->name('login_admin');

Route::inertia('/find_account_user', 'Find_Account_User')->name('find_account_user');
Route::get('/account/find', [AccountController::class, 'find'])->name('account.find');
Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

Route::inertia("/register_employee", 'Auth/Register_Employee')->name('registration_employee');
Route::post('/register-employee', [RegisterOfficialController::class, 'store'])->name('register_employee.store');

Route::inertia("/register_resident", 'Auth/Register_Resident')->name('registration_resident');
Route::post('/register-resident', [RegisterResidentController::class, 'store'])->name('register_resident.store');
// Route::post("/register_resident", [UsersController::class, 'store'])->name('register_resident.store');

Route::inertia("/guest_discussion", 'Guest_Discussion')->name('guest_discussion');

Route::inertia("/guest_announcement", 'Guest_Announcement')->name('guest_announcement');


// Resident Routes
// Authenticated routes
Route::middleware('auth')->group(function () {

    // Announcements (Resident – view only)
    Route::get('/resident/announcements', [ResidentAnnouncementController::class, 'index'])->name('announcement_resident');
    Route::get('/resident/announcements/{id}', [ResidentAnnouncementController::class, 'show'])->name('announcement_clickpost_resident');

    // Discussion routes (Resident and Employee – can post)
    Route::get('/discussion', [DiscussionController::class, 'index'])->name('discussion_resident');
    Route::get('/discussion/create', [DiscussionController::class, 'create'])->name('discussion_addpost_resident');
    Route::post('/discussion/store', [DiscussionController::class, 'store'])->name('posts.store');

    // General posts
    Route::delete('/posts/{postId}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comments
    Route::delete('/comments/{commentId}', [PostCommentController::class, 'destroy'])->name('comments.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/r_document_request_select', [DocumentRequestController::class, 'selectPage'])->name('document_request_select_resident');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/requests', [DocumentRequestController::class, 'store'])->name('requests.store');
});

Route::inertia("/r_document_request_description", 'User/Resident/R_Document_Request_Description')->name('document_request_description_resident');

Route::inertia("/r_document_request_form", 'User/Resident/R_Document_Request_Form')->name('document_request_form_resident');

Route::inertia("/r_document_request_submission", 'User/Resident/R_Document_Request_Submission')->name('document_request_submission_resident');

Route::middleware(['auth'])->group(function () {
    Route::get('/r_event_assist', [EventAssistanceController::class, 'selectPage'])->name('event_assistance_resident');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/event-assistance', [EventAssistanceRequestController::class, 'store'])
        ->name('event_assist.store');
    
    // Event Assistance Appeal/Resubmission routes
    Route::get('/event-assistance/{id}/appeal', [EventAssistanceRequestController::class, 'appealForm'])
        ->name('event_assistance_appeal');
    
    Route::post('/event-assistance/{id}/appeal', [EventAssistanceRequestController::class, 'appeal'])
        ->name('event_assistance.appeal');
    
    Route::put('/event-assistance/{id}', [EventAssistanceRequestController::class, 'update'])
        ->name('event_assistance.update');
});

Route::inertia("/r_notification_activities", 'User/Resident/R_Notification_Activities')->name('notification_activities_resident');

Route::middleware(['auth'])->group(function () {
    Route::get('/r_notification_request', [NotificationRequestController::class, 'index'])
         ->name('notification_request_resident');
});

Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

Route::inertia("/r_notification_pick_info", 'User/Resident/R_Notification_Pick_Info')->name('notification_pick_info_resident');

Route::inertia("/r_payment", 'User/Resident/R_Payment')->name('payment_resident');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/r_profile', [ProfileController::class, 'edit'])->name('profile_resident');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.update_picture');
});


Route::inertia("/r_help_center", 'User/Resident/R_Help_Center')->name('help_center_resident');

// Employee Routes
// Redirect old paths to new controller routes
Route::get('/e_announcement', function () {
    return redirect()->route('announcement_employee');
})->middleware('auth');

Route::get('/e_discussion', function () {
    return redirect()->route('discussion_resident');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    // Redirect old employee discussion route to main discussion route
    Route::get('/employee/discussion', function () {
        return redirect()->route('discussion_resident');
    })->name('discussion_employee');
    
    // Employee discussion create/store routes - redirect to main discussion routes
    Route::get('/employee/discussion/create', function () {
        return redirect()->route('discussion_addpost_resident');
    })->name('discussion_addpost_employee');
    
    Route::post('/employee/discussion/store', [DiscussionController::class, 'store'])->name('posts_employee.store');

    Route::get('/employee/announcement', [AnnouncementController::class, 'index'])->name('announcement_employee');
    Route::get('/employee/announcement/create', [AnnouncementController::class, 'create'])->name('announcement_addpost_employee');
    Route::post('/employee/announcement/store', [AnnouncementController::class, 'store'])->name('announcement_employee.store');
    Route::get('/employee/announcement/{id}', [AnnouncementController::class, 'show'])->name('announcement_clickpost_employee');

    // Note: This route is kept for backward compatibility but should not be used
    // Residents should use /discussion/store (posts.store) which goes to DiscussionController
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store.legacy');
});

Route::inertia("/e_document_request_select", 'User/Employee/E_Document_Request_Select')->name('document_request_select_employee');

Route::inertia("/e_document_request_description", 'User/Employee/E_Document_Request_Description')->name('document_request_description_employee');

Route::inertia("/e_document_request_form", 'User/Employee/E_Document_Request_Form')->name('document_request_form_employee');

Route::inertia("/e_document_request_submission", 'User/Employee/E_Document_Request_Submission')->name('document_request_submission_employee');

Route::inertia("/e_event_assist", 'User/Employee/E_Event_Assist')->name('event_assistance_employee');

Route::inertia("/e_notification_activities", 'User/Employee/E_Notification_Activities')->name('notification_activities_employee');

Route::middleware(['auth'])->group(function () {
    Route::get('/e_notification_request', [NotificationRequestController::class, 'OfficialReq'])
         ->name('notification_request_employee');
});

Route::inertia("/e_notification_pick_info", 'User/Employee/E_Notification_Pick_Info')->name('notification_pick_info_employee');

Route::inertia("/e_payment", 'User/Employee/E_Payment')->name('payment_employee');

Route::middleware(['auth'])->group(function () {
    Route::get('/e_profile', [ProfileController::class, 'employeeProfile'])->name('profile_employee');
    Route::post('/e_profile/update-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile_employee.update_picture');
    
    // Post reactions
    Route::post('/posts/{postId}/reactions', [PostReactionController::class, 'toggle'])->name('posts.reactions.toggle');
    Route::get('/posts/{postId}/reactions', [PostReactionController::class, 'getReactions'])->name('posts.reactions.get');
    
    // Post comments
    Route::post('/posts/{postId}/comments', [PostCommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{postId}/comments', [PostCommentController::class, 'getComments'])->name('posts.comments.get');
    Route::get('/posts/{postId}/comments/count', [PostCommentController::class, 'getCommentCount'])->name('posts.comments.count');
    
    // Comment reactions
    Route::post('/comments/{commentId}/reactions', [CommentReactionController::class, 'toggle'])->name('comments.reactions.toggle');
    
    // Tags API
    Route::get('/api/tags/trending', [TagController::class, 'trending'])->name('api.tags.trending');
    
    // Notifications API
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::put('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
    Route::put('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
    
    // Posts API - Check if posts exist
    Route::post('/api/posts/check', [PostController::class, 'checkPosts'])->name('api.posts.check');
    
    // Poll routes
    Route::post('/polls/{pollId}/vote', [\App\Http\Controllers\PollController::class, 'vote'])->name('polls.vote');
    Route::get('/posts/{postId}/poll', [\App\Http\Controllers\PollController::class, 'getPoll'])->name('posts.poll');
});

Route::inertia("/e_help_center", 'User/Employee/E_Help_Center')->name('help_center_employee');

// Approver Routes
Route::inertia("/a_dashboard", 'Admin/Approver/A_Dashboard')->name('dashboard_approver');

Route::get('/a_document_request', [DocumentRequestController::class, 'docuReq'])
     ->middleware(['auth']) // make sure it matches your auth middleware
     ->name('document_request_approver');

// GET /document-requests/{id}/valid-id
Route::get('/document-requests/{id}/valid-id', [DocumentRequestController::class, 'validIdContent'])
    ->name('document_requests.valid_id')
    ->middleware(['auth']);

Route::post('/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])
    ->name('document_requests.approve')
    ->middleware(['auth']);

Route::post('/document-requests/{id}/reject', [DocumentRequestController::class, 'reject'])
    ->name('document_requests.reject')
    ->middleware(['auth']);

// Appeal/reapply rejected request
Route::get('/document-requests/{id}/appeal', [DocumentRequestController::class, 'appealForm'])
    ->name('document_request_appeal')
    ->middleware(['auth']);

Route::post('/document-requests/{id}/appeal', [DocumentRequestController::class, 'appeal'])
    ->name('document_requests.appeal')
    ->middleware(['auth']);

Route::put('/document-requests/{id}', [DocumentRequestController::class, 'update'])
    ->name('document_requests.update')
    ->middleware(['auth']);

// Download generated document
Route::get('/document-requests/{id}/download/{format}', [DocumentRequestController::class, 'download'])
    ->name('document_requests.download')
    ->middleware(['auth'])
    ->where('format', 'pdf|docx');

Route::inertia("/a_event_request", 'Admin/Approver/A_Event_Request')->name('event_request_approver');
Route::get('/a_event_request', [EventAssistanceRequestController::class, 'index'])
    ->middleware(['auth'])
    ->name('event_request_approver');

// keep or remove the existing controller route if you don't need both:
Route::get('/approver/event-requests', [EventAssistanceRequestController::class,'index'])
    ->name('approver.event_requests');

Route::inertia("/a_history", 'Admin/Approver/A_History')->name('history_approver');

// Treasurer Routes
Route::inertia("/t_dashboard", 'Admin/Treasurer/T_Dashboard')->name('dashboard_treasurer');

Route::inertia("/t_view_payment", 'Admin/Treasurer/T_View_Payment')->name('view_payment_treasurer');
Route::get('/t_view_payment', [PaymentController::class, 'index'])
    ->name('view_payment_treasurer');
Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])
    ->name('payments.update_status')
    ->middleware('auth');

Route::inertia("/t_history", 'Admin/Treasurer/T_History')->name('history_treasurer');
Route::get('/t_history', [PaymentController::class, 'history'])
    ->name('history_treasurer');

// System Admin Route
Route::get("/s_dashboard", [DashboardController::class, 'index'])
    ->name('dashboard_admin')
    ->middleware('auth');

// User management actions
Route::post('/admin/users/{userId}/restrict', [DashboardController::class, 'restrictUser'])
    ->name('admin.users.restrict')
    ->middleware('auth');

Route::post('/admin/users/{userId}/unrestrict', [DashboardController::class, 'unrestrictUser'])
    ->name('admin.users.unrestrict')
    ->middleware('auth');

Route::post('/admin/users/{userId}/password', [DashboardController::class, 'changePassword'])
    ->name('admin.users.password')
    ->middleware('auth');

Route::delete('/admin/users/{userId}', [DashboardController::class, 'deleteUser'])
    ->name('admin.users.delete')
    ->middleware('auth');

Route::inertia("/s_history", 'Admin/System_Admin/S_History')->name('history_admin');

Route::inertia("/s_users", 'Admin/System_Admin/S_Users')->name('users_admin');

Route::inertia("/s_register_request", 'Admin/System_Admin/S_Register_Request')->name('register_request_admin');
Route::get('/s_register_request', [RegisterRequestController::class, 'index'])
    ->name('register_request_admin')
    ->middleware('auth'); // keep auth middleware if you use it

// Approve / Reject endpoints used by the Vue front-end
Route::post('/admin/register-requests/{id}/approve', [RegisterRequestController::class, 'approve'])
    ->name('admin.register_requests.approve')
    ->middleware('auth');

Route::post('/admin/register-requests/{id}/reject', [RegisterRequestController::class, 'reject'])
    ->name('admin.register_requests.reject')
    ->middleware('auth');

// Approve / Reject actions called from the Vue component
// GET /approver/event-requests/{id}/valid-id
Route::get('/approver/event-requests/{id}/valid-id', [EventAssistanceRequestController::class, 'validId'])
    ->name('event_requests.valid_id')
    ->middleware(['auth']);

Route::post('/approver/event-requests/{id}/approve', [EventAssistanceRequestController::class, 'approve'])
    ->name('approver.event_requests.approve');

Route::post('/approver/event-requests/{id}/reject', [EventAssistanceRequestController::class, 'reject'])
    ->name('approver.event_requests.reject');


Route::inertia("/s_records", 'Admin/System_Admin/S_Records')->name('records_admin');

Route::get('/s_report', [ReportController::class, 'index'])
    ->name('report_admin')
    ->middleware('auth');

// Report routes
Route::middleware(['auth'])->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/{id}/dismiss', [ReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::delete('/admin/posts/{postId}', [ReportController::class, 'deletePost'])->name('admin.posts.delete');
    Route::post('/admin/contact_messages/{id}', [ReportController::class, 'updateContactMessage'])->name('admin.contact_messages.update');
});

// History API routes
Route::middleware(['auth'])->prefix('api/history')->group(function () {
    Route::get('/officials', [\App\Http\Controllers\HistoryController::class, 'getOfficials'])->name('api.history.officials');
    Route::get('/reports', [\App\Http\Controllers\HistoryController::class, 'getReports'])->name('api.history.reports');
    Route::get('/messages', [\App\Http\Controllers\HistoryController::class, 'getMessages'])->name('api.history.messages');
});

// OTP routes for registration (no auth required)
Route::post('/otp/send', [\App\Http\Controllers\OtpController::class, 'send'])->name('otp.send');
Route::post('/otp/verify', [\App\Http\Controllers\OtpController::class, 'verify'])->name('otp.verify');
Route::post('/otp/check-verification', [\App\Http\Controllers\OtpController::class, 'checkVerification'])->name('otp.check-verification');

// Forgot password routes (no auth required)
Route::post('/forgot-password/send-otp', [\App\Http\Controllers\ForgotPasswordController::class, 'sendOtp'])->name('forgot-password.send-otp');
Route::post('/forgot-password/verify-otp', [\App\Http\Controllers\ForgotPasswordController::class, 'verifyOtp'])->name('forgot-password.verify-otp');
Route::post('/forgot-password/reset', [\App\Http\Controllers\ForgotPasswordController::class, 'resetPassword'])->name('forgot-password.reset');

