<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\StudentAppointmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonalDataSheetController;
use App\Http\Controllers\FeedbackFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\AuthorizedIdController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/today-appointments', [DashboardController::class, 'todayAppointments'])->name('today.appointments');
    Route::get('/pending-appointments', [DashboardController::class, 'pendingAppointments'])->name('pending.appointments');
    
    // Counselor and Assistant Shared Features (Read-only for assistants)
    Route::middleware('counselor_or_assistant')->group(function () {
        // Session History - View only for assistants
        Route::get('/appointments/session-history', [AppointmentController::class, 'sessionHistory'])->name('appointments.session-history');
        Route::get('/appointments/statistics', [AppointmentController::class, 'getStatistics'])->name('appointments.statistics');
        
        // Student Management - View only for assistants
        Route::get('/students', [StudentManagementController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [StudentManagementController::class, 'show'])->name('students.show');
        Route::get('/students/statistics', [StudentManagementController::class, 'getStatistics'])->name('students.statistics');
        
        // Activity Logs - View only for assistants
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
    
    // Counselor Only Features (Full privileges)
    Route::middleware('counselor_only')->group(function () {
        // Reports (Counselor only)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/appointments', [ReportController::class, 'appointmentReport'])->name('reports.appointments');
        Route::get('/reports/clients', [ReportController::class, 'clientReport'])->name('reports.clients');
        Route::get('/reports/feedback', [ReportController::class, 'feedbackReport'])->name('reports.feedback');
        Route::get('/reports/export', [ReportController::class, 'exportReport'])->name('reports.export');
        
        // Export functionality (Counselor only)
        Route::get('/appointments/export-history', [AppointmentController::class, 'exportSessionHistory'])->name('appointments.export-history');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/students/export', [StudentManagementController::class, 'export'])->name('students.export');
        
        // Student PDS access (Counselor only)
        Route::get('/students/{student}/pds', [StudentManagementController::class, 'showPds'])->name('students.pds');
        
        // User Management (Counselor only)
        Route::resource('users', UserManagementController::class);
        
        // System Backup (Counselor only)
        Route::get('/system/backup', [SystemController::class, 'backup'])->name('system.backup');
        Route::get('/system/backup/download', [SystemController::class, 'downloadBackup'])->name('system.backup.download');
        
        // Authorized IDs Management (Counselor only)
        Route::resource('authorized-ids', AuthorizedIdController::class);
        Route::get('/authorized-ids/export', [AuthorizedIdController::class, 'export'])->name('authorized-ids.export');
        Route::post('/authorized-ids/bulk-destroy', [AuthorizedIdController::class, 'bulkDestroy'])->name('authorized-ids.bulk-destroy');
    });
    
    // Appointments - Different access levels
    Route::middleware('counselor_or_assistant')->group(function () {
        // View appointments (both can view)
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    });
    
    // Student appointment access - dedicated controller
    Route::middleware('auth')->group(function () {
        Route::get('/student/appointments', [StudentAppointmentController::class, 'index'])->name('student.appointments.index');
        Route::get('/student/appointments/create', [StudentAppointmentController::class, 'create'])->name('student.appointments.create');
        Route::post('/student/appointments', [StudentAppointmentController::class, 'store'])->name('student.appointments.store');
        Route::get('/student/appointments/session-history', [StudentAppointmentController::class, 'sessionHistory'])->name('student.appointments.session-history');
        Route::get('/student/appointments/export-history', [StudentAppointmentController::class, 'exportSessionHistory'])->name('student.appointments.export-history');
        Route::get('/student/appointments/{appointment}', [StudentAppointmentController::class, 'show'])->whereNumber('appointment')->name('student.appointments.show');
        Route::get('/student/appointments/{appointment}/edit', [StudentAppointmentController::class, 'edit'])->whereNumber('appointment')->name('student.appointments.edit');
        Route::put('/student/appointments/{appointment}', [StudentAppointmentController::class, 'update'])->whereNumber('appointment')->name('student.appointments.update');
        Route::patch('/student/appointments/{appointment}/cancel', [StudentAppointmentController::class, 'cancel'])->whereNumber('appointment')->name('student.appointments.cancel');
        Route::get('/api/student/counselors/{counselor}/available-slots', [StudentAppointmentController::class, 'getAvailableSlots'])->name('api.student.counselors.available-slots');
    });
    
    Route::middleware('counselor_only')->group(function () {
        // Full appointment management (counselor only)
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        
        // Appointment approval/rejection (counselor only)
        Route::patch('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
        Route::patch('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
        Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    });
    

    
    // User Activity Logs (All authenticated users)
    Route::get('/my-activity', [ActivityLogController::class, 'userActivity'])->name('activity-logs.user-activity');
    
    // API endpoint for available time slots
    Route::get('/api/counselors/{counselor}/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('api.counselors.available-slots');
    
    // Schedules (Counselor only - full management)
    Route::middleware('counselor_only')->group(function () {
        Route::resource('schedules', ScheduleController::class);
    });
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    Route::middleware('counselor_only')->group(function () {
        // Send notifications (counselor only)
        Route::post('/notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Personal Data Sheet (Students only)
    Route::middleware('student')->group(function () {
        Route::get('/pds', [PersonalDataSheetController::class, 'show'])->name('pds.show');
        Route::get('/pds/edit', [PersonalDataSheetController::class, 'edit'])->name('pds.edit');
        Route::patch('/pds', [PersonalDataSheetController::class, 'update'])->name('pds.update');
        Route::post('/pds/auto-save', [PersonalDataSheetController::class, 'autoSave'])->name('pds.auto-save');
    });
    
    // Feedback Forms (Students only)
    Route::middleware('student')->group(function () {
        Route::resource('feedback', FeedbackFormController::class);
    });
});

require __DIR__.'/auth.php';
