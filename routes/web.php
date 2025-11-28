<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\EventAssistanceController;

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


Route::inertia("/", 'Welcome')->name('welcome');

Route::inertia("/login_user", 'Login_User')->name('login');
Route::post('/login/check', [LoginController::class, 'check'])->name('login.check');

Route::inertia("/login_employee", 'Login_employee')->name('login_employee');

Route::inertia("/login_moderator", 'Admin/Login_Moderator')->name('login_moderator');

Route::inertia("/login_approver", 'Admin/Login_Approver')->name('login_approver');

Route::inertia("/login_treasurer", 'Admin/Login_Treasurer')->name('login_treasurer');

Route::inertia("/login_admin", 'Admin/Login_Admin')->name('login_admin');

Route::inertia('/find_account_user', 'Find_Account_User')->name('find_account_user');
Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

Route::inertia("/register_employee", 'Auth/Register_Employee')->name('registration_employee');
Route::post('/register-employee', [RegisterOfficialController::class, 'store'])->name('register_employee.store');

Route::inertia("/register_resident", 'Auth/Register_Resident')->name('registration_resident');
Route::post('/register-resident', [RegisterResidentController::class, 'store'])->name('register_resident.store');
// Route::post("/register_resident", [UsersController::class, 'store'])->name('register_resident.store');

Route::inertia("/guest_discussion", 'Guest_Discussion')->name('guest_discussion');

Route::inertia("/guest_announcement", 'Guest_Announcement')->name('guest_announcement');


// Resident Routes
Route::inertia("/r_discussion", 'User/Resident/R_Discussion')->name('discussion_resident');

Route::inertia("/r_discussion_addpost", 'User/Resident/R_Discussion_AddPost')->name('discussion_addpost_resident');

Route::inertia("/r_announcement", 'User/Resident/R_Announcement')->name('announcement_resident');

Route::inertia("/r_announcement_clickpost", 'User/Resident/R_Announcement_ClickPost')->name('announcement_clickpost_resident');

Route::inertia("/r_document_request_select", 'User/Resident/R_Document_Request_Select')->name('document_request_select_resident');
Route::middleware(['auth'])->group(function () {
    Route::post('/requests', [DocumentRequestController::class, 'store'])->name('requests.store');
});


Route::inertia("/r_document_request_description", 'User/Resident/R_Document_Request_Description')->name('document_request_description_resident');

Route::inertia("/r_document_request_form", 'User/Resident/R_Document_Request_Form')->name('document_request_form_resident');

Route::inertia("/r_document_request_submission", 'User/Resident/R_Document_Request_Submission')->name('document_request_submission_resident');

Route::inertia("/r_event_assist", 'User/Resident/R_Event_Assist')->name('event_assistance_resident');
// Route::get('/r_event_assist', [EventAssistanceController::class, 'create'])
//     ->name('event_assistance.create');

// // store submission
// Route::post('/r_event_assist', [EventAssistanceController::class, 'store'])
//     ->name('event_assistance.store');

Route::inertia("/r_notification_activities", 'User/Resident/R_Notification_Activities')->name('notification_activities_resident');

Route::inertia("/r_notification_request", 'User/Resident/R_Notification_Request')->name('notification_request_resident');
Route::middleware(['auth'])->group(function () {
    // single route that renders via controller and is protected
    Route::get('/r_notification_request', [NotificationRequestController::class, 'index'])
         ->name('notification_request_resident');
});
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

Route::inertia("/r_notification_pick_info", 'User/Resident/R_Notification_Pick_Info')->name('notification_pick_info_resident');

Route::inertia("/r_payment", 'User/Resident/R_Payment')->name('payment_resident');

Route::inertia("/r_profile", 'User/Resident/R_Profile')->name('profile_resident');

Route::inertia("/r_help_center", 'User/Resident/R_Help_Center')->name('help_center_resident');

// Employee Routes
Route::inertia("/e_discussion", 'User/Employee/E_Discussion')->name('discussion_employee');

Route::inertia("/e_discussion_addpost", 'User/Employee/E_Discussion_AddPost')->name('discussion_addpost_employee');

Route::inertia("/e_announcement", 'User/Employee/E_Announcement')->name('announcement_employee');

Route::inertia("/e_announcement_addpost", 'User/Employee/E_Announcement_AddPost')->name('announcement_addpost_employee');

Route::inertia("/e_announcement_clickpost", 'User/Employee/E_Announcement_ClickPost')->name('announcement_clickpost_employee');

Route::inertia("/e_document_request_select", 'User/Employee/E_Document_Request_Select')->name('document_request_select_employee');

Route::inertia("/e_document_request_description", 'User/Employee/E_Document_Request_Description')->name('document_request_description_employee');

Route::inertia("/e_document_request_form", 'User/Employee/E_Document_Request_Form')->name('document_request_form_employee');

Route::inertia("/e_document_request_submission", 'User/Employee/E_Document_Request_Submission')->name('document_request_submission_employee');

Route::inertia("/e_event_assist", 'User/Employee/E_Event_Assist')->name('event_assistance_employee');

Route::inertia("/e_notification_activities", 'User/Employee/E_Notification_Activities')->name('notification_activities_employee');

Route::inertia("/e_notification_request", 'User/Employee/E_Notification_Request')->name('notification_request_employee');
Route::middleware(['auth'])->group(function () {
    Route::get('/e_notification_request', [NotificationRequestController::class, 'OfficialReq'])
         ->name('notification_request_employee');
});

Route::inertia("/e_notification_pick_info", 'User/Employee/E_Notification_Pick_Info')->name('notification_pick_info_employee');

Route::inertia("/e_payment", 'User/Employee/E_Payment')->name('payment_employee');

Route::inertia("/e_profile", 'User/Employee/E_Profile')->name('profile_employee');

Route::inertia("/e_help_center", 'User/Employee/E_Help_Center')->name('help_center_employee');

// Approver Routes
Route::inertia("/a_dashboard", 'Admin/Approver/A_Dashboard')->name('dashboard_approver');

Route::inertia("/a_document_request", 'Admin/Approver/A_Document_Request')->name('document_request_approver');
Route::get('/a_document_request', [DocumentRequestController::class, 'docuReq'])
     ->middleware(['auth']) // make sure it matches your auth middleware
     ->name('document_request_approver');

Route::post('/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])
    ->name('document_requests.approve')
    ->middleware(['auth']);

Route::post('/document-requests/{id}/reject', [DocumentRequestController::class, 'reject'])
    ->name('document_requests.reject')
    ->middleware(['auth']);

Route::inertia("/a_event_request", 'Admin/Approver/A_Event_Request')->name('event_request_approver');

Route::inertia("/a_history", 'Admin/Approver/A_History')->name('history_approver');

// Treasurer Routes
Route::inertia("/t_dashboard", 'Admin/Treasurer/T_Dashboard')->name('dashboard_treasurer');

Route::inertia("/t_view_payment", 'Admin/Treasurer/T_View_Payment')->name('view_payment_treasurer');

Route::inertia("/t_history", 'Admin/Treasurer/T_History')->name('history_treasurer');

// System Admin Route
Route::inertia("/s_dashboard", 'Admin/System_Admin/S_Dashboard')->name('dashboard_admin');

Route::inertia("/s_history", 'Admin/System_Admin/S_History')->name('history_admin');

Route::inertia("/s_users", 'Admin/System_Admin/S_Users')->name('users_admin');

Route::inertia("/s_register_request", 'Admin/System_Admin/S_Register_Request')->name('register_request_admin');
Route::get('/s_register_request', [RegisterRequestController::class, 'index'])
    ->name('register_request_admin')
    ->middleware('auth'); // keep auth middleware if you use it

// Approve / Reject actions called from the Vue component
Route::post('/admin/register_requests/{id}/approve', [RegisterRequestController::class, 'approve'])
    ->name('admin.register_requests.approve')
    ->middleware('auth');

Route::post('/admin/register_requests/{id}/reject', [RegisterRequestController::class, 'reject'])
    ->name('admin.register_requests.reject')
    ->middleware('auth');

Route::inertia("/s_records", 'Admin/System_Admin/S_Records')->name('records_admin');

Route::inertia("/s_report", 'Admin/System_Admin/S_Report')->name('report_admin');

