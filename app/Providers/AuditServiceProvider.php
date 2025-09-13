<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;
use App\Models\User;
use App\Models\Appointment;
use App\Models\FeedbackForm;
use App\Models\PersonalDataSheet;

class AuditServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureModelEvents();
        $this->configureAuthEvents();
        $this->configureSystemEvents();
    }

    /**
     * Configure model events for audit logging
     */
    protected function configureModelEvents(): void
    {
        // User model events
        Event::listen('eloquent.created: ' . User::class, function ($user) {
            app(AuditService::class)->logDataModification('User', 'create', [], $user->toArray());
        });

        Event::listen('eloquent.updated: ' . User::class, function ($user) {
            app(AuditService::class)->logDataModification('User', 'update', $user->getOriginal(), $user->toArray());
        });

        Event::listen('eloquent.deleted: ' . User::class, function ($user) {
            app(AuditService::class)->logDataModification('User', 'delete', $user->toArray(), []);
        });

        // Appointment model events
        Event::listen('eloquent.created: ' . Appointment::class, function ($appointment) {
            app(AuditService::class)->logDataModification('Appointment', 'create', [], $appointment->toArray());
        });

        Event::listen('eloquent.updated: ' . Appointment::class, function ($appointment) {
            app(AuditService::class)->logDataModification('Appointment', 'update', $appointment->getOriginal(), $appointment->toArray());
        });

        Event::listen('eloquent.deleted: ' . Appointment::class, function ($appointment) {
            app(AuditService::class)->logDataModification('Appointment', 'delete', $appointment->toArray(), []);
        });

        // FeedbackForm model events
        Event::listen('eloquent.created: ' . FeedbackForm::class, function ($feedback) {
            app(AuditService::class)->logDataModification('FeedbackForm', 'create', [], $feedback->toArray());
        });

        Event::listen('eloquent.updated: ' . FeedbackForm::class, function ($feedback) {
            app(AuditService::class)->logDataModification('FeedbackForm', 'update', $feedback->getOriginal(), $feedback->toArray());
        });

        Event::listen('eloquent.deleted: ' . FeedbackForm::class, function ($feedback) {
            app(AuditService::class)->logDataModification('FeedbackForm', 'delete', $feedback->toArray(), []);
        });

        // PersonalDataSheet model events
        Event::listen('eloquent.created: ' . PersonalDataSheet::class, function ($pds) {
            app(AuditService::class)->logDataModification('PersonalDataSheet', 'create', [], $pds->toArray());
        });

        Event::listen('eloquent.updated: ' . PersonalDataSheet::class, function ($pds) {
            app(AuditService::class)->logDataModification('PersonalDataSheet', 'update', $pds->getOriginal(), $pds->toArray());
        });

        Event::listen('eloquent.deleted: ' . PersonalDataSheet::class, function ($pds) {
            app(AuditService::class)->logDataModification('PersonalDataSheet', 'delete', $pds->toArray(), []);
        });
    }

    /**
     * Configure authentication events
     */
    protected function configureAuthEvents(): void
    {
        Event::listen('Illuminate\Auth\Events\Login', function ($event) {
            app(AuditService::class)->logAuthEvent('login', [
                'email' => $event->user->email,
                'success' => true,
                'user_id' => $event->user->id,
            ]);
        });

        Event::listen('Illuminate\Auth\Events\Failed', function ($event) {
            app(AuditService::class)->logAuthEvent('failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'success' => false,
                'failure_reason' => 'Invalid credentials',
            ]);
        });

        Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
            app(AuditService::class)->logAuthEvent('logout', [
                'email' => $event->user->email ?? 'unknown',
                'success' => true,
                'user_id' => $event->user->id ?? null,
            ]);
        });

        Event::listen('Illuminate\Auth\Events\PasswordReset', function ($event) {
            app(AuditService::class)->logAuthEvent('password_reset', [
                'email' => $event->user->email,
                'success' => true,
                'user_id' => $event->user->id,
            ]);
        });

        Event::listen('Illuminate\Auth\Events\Registered', function ($event) {
            app(AuditService::class)->logAuthEvent('registered', [
                'email' => $event->user->email,
                'success' => true,
                'user_id' => $event->user->id,
            ]);
        });
    }

    /**
     * Configure system events
     */
    protected function configureSystemEvents(): void
    {
        // File upload events
        Event::listen('file.uploaded', function ($event) {
            app(AuditService::class)->logFileOperation('upload', $event->filename, [
                'file_size' => $event->fileSize,
                'file_type' => $event->fileType,
                'upload_path' => $event->uploadPath,
                'user_id' => $event->userId,
            ]);
        });

        // File download events
        Event::listen('file.downloaded', function ($event) {
            app(AuditService::class)->logFileOperation('download', $event->filename, [
                'file_size' => $event->fileSize,
                'user_id' => $event->userId,
            ]);
        });

        // File deletion events
        Event::listen('file.deleted', function ($event) {
            app(AuditService::class)->logFileOperation('delete', $event->filename, [
                'user_id' => $event->userId,
                'reason' => $event->reason ?? 'User request',
            ]);
        });

        // System maintenance events
        Event::listen('system.maintenance.start', function ($event) {
            app(AuditService::class)->logSystemEvent('maintenance_start', [
                'reason' => $event->reason,
                'duration' => $event->duration,
                'initiated_by' => $event->initiatedBy,
            ]);
        });

        Event::listen('system.maintenance.end', function ($event) {
            app(AuditService::class)->logSystemEvent('maintenance_end', [
                'duration' => $event->duration,
                'status' => $event->status,
            ]);
        });

        // Backup events
        Event::listen('system.backup.start', function ($event) {
            app(AuditService::class)->logSystemEvent('backup_start', [
                'type' => $event->type,
                'destination' => $event->destination,
            ]);
        });

        Event::listen('system.backup.completed', function ($event) {
            app(AuditService::class)->logSystemEvent('backup_completed', [
                'type' => $event->type,
                'size' => $event->size,
                'duration' => $event->duration,
                'status' => 'success',
            ]);
        });

        Event::listen('system.backup.failed', function ($event) {
            app(AuditService::class)->logSystemEvent('backup_failed', [
                'type' => $event->type,
                'error' => $event->error,
                'status' => 'failed',
            ]);
        });

        // Security events
        Event::listen('security.threat.detected', function ($event) {
            app(AuditService::class)->logSecurityEvent('threat_detected', [
                'threat_type' => $event->threatType,
                'risk_level' => $event->riskLevel,
                'source_ip' => $event->sourceIp,
                'details' => $event->details,
            ]);
        });

        Event::listen('security.mitigation.applied', function ($event) {
            app(AuditService::class)->logSecurityEvent('mitigation_applied', [
                'threat_type' => $event->threatType,
                'mitigation_type' => $event->mitigationType,
                'effectiveness' => $event->effectiveness,
                'details' => $event->details,
            ]);
        });

        // API usage events
        Event::listen('api.request', function ($event) {
            app(AuditService::class)->logApiUsage($event->endpoint, [
                'method' => $event->method,
                'user_id' => $event->userId,
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        });

        Event::listen('api.response', function ($event) {
            app(AuditService::class)->logApiUsage($event->endpoint, [
                'response_time' => $event->responseTime,
                'status_code' => $event->statusCode,
                'response_size' => $event->responseSize,
            ]);
        });
    }
}
