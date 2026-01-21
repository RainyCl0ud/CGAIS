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
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PendingEmailChangeController;
use App\Http\Controllers\PdfPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware(['auth', 'verified'])->group(function () {
Route::middleware(['auth','verified'])->group(function () {
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

        // Course Management - View only for assistants
        Route::resource('courses', CourseController::class)->except(['destroy']);
        Route::patch('courses/{course}/toggle', [CourseController::class, 'toggle'])->name('courses.toggle');
        
        // Activity Logs - View only for assistants
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
    
    // Counselor and Assistant Features (Full privileges except Counselor profile editing)
    Route::middleware('counselor_or_assistant')->group(function () {
        // Reports (Counselor and Assistant)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/appointments', [ReportController::class, 'appointmentReport'])->name('reports.appointments');
        Route::get('/reports/clients', [ReportController::class, 'clientReport'])->name('reports.clients');
        Route::get('/reports/feedback', [ReportController::class, 'feedbackReport'])->name('reports.feedback');
        Route::get('/reports/export', [ReportController::class, 'exportReport'])->name('reports.export');

        // Export functionality (Counselor and Assistant)
        Route::get('/appointments/export-history', [AppointmentController::class, 'exportSessionHistory'])->name('appointments.export-history');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/students/export', [StudentManagementController::class, 'export'])->name('students.export');

        // User Management (Counselor and Assistant, but with restrictions)
        Route::resource('users', UserManagementController::class);

        // Authorized IDs Management (Counselor and Assistant)
        Route::resource('authorized-ids', AuthorizedIdController::class);
        Route::get('/authorized-ids/export', [AuthorizedIdController::class, 'export'])->name('authorized-ids.export');
        Route::post('/authorized-ids/bulk-destroy', [AuthorizedIdController::class, 'bulkDestroy'])->name('authorized-ids.bulk-destroy');

        // Document Codes Management (Counselor and Assistant)
        Route::get('document-codes', [App\Http\Controllers\DocumentCodeController::class, 'index'])->name('document-codes.index');
        Route::get('document-codes/{type}/edit', [App\Http\Controllers\DocumentCodeController::class, 'edit'])->name('document-codes.edit');
        Route::put('document-codes', [App\Http\Controllers\DocumentCodeController::class, 'update'])->name('document-codes.update');
    });
    
    // Restricted features - Counselor only (Student PDS and System Backup)
    Route::middleware('counselor_only')->group(function () {
        // Student PDS directory access (Counselor only)
        Route::get('/students/{student}/pds', [StudentManagementController::class, 'showPds'])->name('students.pds');
        // Print PDS to server-side PDF
        Route::get('/students/{student}/pds/print', [StudentManagementController::class, 'printPds'])->name('students.pds.print');
        // Printable HTML view (opens in browser for printing) and generate+save PDF endpoint
        Route::get('/students/{student}/pds/print-view', [StudentManagementController::class, 'printViewPds'])->name('students.pds.print-view');
        Route::post('/students/{student}/pds/generate', [StudentManagementController::class, 'generatePdsPdf'])->name('students.pds.generate');

        // System Backup (Counselor only)
        Route::get('/system/backup', [SystemController::class, 'backup'])->name('system.backup');
        Route::post('/system/backup/create', [SystemController::class, 'createManualBackup'])->name('system.backup.create');
        Route::get('/system/backup/download', [SystemController::class, 'downloadBackup'])->name('system.backup.download');
        Route::get('/system/backup/download/{filename}', [SystemController::class, 'downloadBackupFile'])->name('system.backup.download-file');
            // Counselor-only: Create new counselor account from profile page
            Route::post('/profile/create-counselor', [ProfileController::class, 'createCounselor'])->name('profile.create-counselor');
            // Deactivate counselor account
            Route::post('/profile/deactivate-counselor', [ProfileController::class, 'deactivateCounselor'])->name('profile.deactivate-counselor');
            // Services management for counselors
            Route::resource('services', App\Http\Controllers\ServiceController::class)->only(['index','store','update']);
            Route::patch('services/{service}/toggle', [App\Http\Controllers\ServiceController::class, 'toggle'])->name('services.toggle');
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
    
    Route::middleware('counselor_or_assistant')->group(function () {
        // Full appointment management (counselor and assistant)
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::match(['PUT', 'PATCH'], '/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        
        // Appointment approval/rejection (counselor and assistant)
        Route::patch('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
        Route::patch('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
        Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::patch('/appointments/{appointment}/put-on-hold', [AppointmentController::class, 'putOnHold'])->name('appointments.put-on-hold');
        Route::patch('/appointments/{appointment}/mark-done', [AppointmentController::class, 'markAsDone'])->name('appointments.mark-done');
    });
    
    // User Activity Logs (All authenticated users)
    Route::get('/my-activity', [ActivityLogController::class, 'userActivity'])->name('activity-logs.user-activity');
    
    // API endpoint for available time slots
    Route::get('/api/counselors/{counselor}/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('api.counselors.available-slots');
    
    // Schedules (Counselor and Assistant - full management)
    Route::middleware('counselor_or_assistant')->group(function () {
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/toggle-unavailable-date', [ScheduleController::class, 'toggleUnavailableDate'])->name('schedules.toggleUnavailableDate');
    });
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    Route::middleware('counselor_or_assistant')->group(function () {
        // Send notifications (counselor and assistant)
        Route::post('/notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/toggle-active', [ProfileController::class, 'toggleCounselorActive'])->name('profile.toggle-active');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pending Email Change
    Route::get('/pending-email/verify/{token}', [PendingEmailChangeController::class, 'verify'])->name('pending-email.verify');
    Route::post('/pending-email/cancel', [PendingEmailChangeController::class, 'cancel'])->name('pending-email.cancel');
    
    // Personal Data Sheet (Students only)
    Route::middleware('student')->group(function () {
        Route::get('/pds', [PersonalDataSheetController::class, 'show'])->name('pds.show');
        Route::get('/pds/print-view', [PersonalDataSheetController::class, 'printView'])->name('pds.print-view');
        Route::post('/pds/generate', [PersonalDataSheetController::class, 'generatePdf'])->name('pds.generate');
        Route::get('/pds/edit', [PersonalDataSheetController::class, 'edit'])->name('pds.edit');
        Route::patch('/pds', [PersonalDataSheetController::class, 'update'])->name('pds.update');
        Route::post('/pds/auto-save', [PersonalDataSheetController::class, 'autoSave'])->name('pds.auto-save');
    });
    
    // PDF preview for authenticated users (server-side DOMPDF)
    Route::get('/preview-pdf', [PdfPreviewController::class, 'previewPdf'])->name('preview.pdf');
    // Feedback Forms (Students, Faculty, and Staff)
    Route::middleware('feedback_access')->group(function () {
        Route::resource('feedback', FeedbackFormController::class)->except(['create']);
        Route::get('/feedback/download/pdf', [FeedbackFormController::class, 'downloadPdf'])->name('feedback.download.pdf');
    });
});

require __DIR__.'/auth.php';
