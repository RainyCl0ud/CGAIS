<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public static function log(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): void {
        $user = auth()->user();
        
        if (!$user) {
            return;
        }

        $data = [
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ];

        if ($request) {
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = $request->userAgent();
        }

        ActivityLog::create($data);
    }

    public static function logAppointmentBooked(Model $appointment, Request $request): void
    {
        self::log(
            'appointment_booked',
            "Booked appointment for {$appointment->appointment_date->format('M d, Y')} at {$appointment->start_time->format('g:i A')}",
            $appointment,
            null,
            $appointment->toArray(),
            $request
        );
    }

    public static function logAppointmentCancelled(Model $appointment, Request $request): void
    {
        self::log(
            'appointment_cancelled',
            "Cancelled appointment for {$appointment->appointment_date->format('M d, Y')} at {$appointment->start_time->format('g:i A')}",
            $appointment,
            null,
            ['status' => 'cancelled'],
            $request
        );
    }

    public static function logAppointmentCompleted(Model $appointment, Request $request): void
    {
        self::log(
            'appointment_completed',
            "Completed appointment for {$appointment->appointment_date->format('M d, Y')} at {$appointment->start_time->format('g:i A')}",
            $appointment,
            null,
            ['status' => 'completed'],
            $request
        );
    }

    public static function logScheduleCreated(Model $schedule, Request $request): void
    {
        self::log(
            'schedule_created',
            "Created schedule for {$schedule->day_of_week} from {$schedule->start_time} to {$schedule->end_time}",
            $schedule,
            null,
            $schedule->toArray(),
            $request
        );
    }

    public static function logScheduleUpdated(Model $schedule, array $oldValues, Request $request): void
    {
        self::log(
            'schedule_updated',
            "Updated schedule for {$schedule->day_of_week}",
            $schedule,
            $oldValues,
            $schedule->toArray(),
            $request
        );
    }

    public static function logFeedbackSubmitted(Model $feedback, Request $request): void
    {
        self::log(
            'feedback_submitted',
            "Submitted feedback for appointment on {$feedback->appointment->appointment_date->format('M d, Y')}",
            $feedback,
            null,
            $feedback->toArray(),
            $request
        );
    }

    public static function logProfileUpdated(array $oldValues, array $newValues, Request $request): void
    {
        self::log(
            'profile_updated',
            'Updated profile information',
            null,
            $oldValues,
            $newValues,
            $request
        );
    }

    public static function logPasswordChanged(Request $request): void
    {
        self::log(
            'password_changed',
            'Changed password',
            null,
            null,
            null,
            $request
        );
    }

    public static function logLogin(Request $request): void
    {
        self::log(
            'login',
            'Logged in to the system',
            null,
            null,
            null,
            $request
        );
    }

    public static function logLogout(Request $request): void
    {
        self::log(
            'logout',
            'Logged out of the system',
            null,
            null,
            null,
            $request
        );
    }

    public static function getRecentActivity(int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getActivityForModel(string $modelType, int $modelId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getActivitySummary(int $userId, int $days = 30): array
    {
        $activities = ActivityLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_activities' => $activities->count(),
            'appointments_booked' => $activities->where('action', 'appointment_booked')->count(),
            'appointments_cancelled' => $activities->where('action', 'appointment_cancelled')->count(),
            'appointments_completed' => $activities->where('action', 'appointment_completed')->count(),
            'schedules_created' => $activities->where('action', 'schedule_created')->count(),
            'feedback_submitted' => $activities->where('action', 'feedback_submitted')->count(),
            'profile_updates' => $activities->where('action', 'profile_updated')->count(),
        ];
    }
}
