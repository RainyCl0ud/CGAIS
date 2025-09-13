<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'password_changed' => 'Password Changed',
            'profile_updated' => 'Profile Updated',
            'appointment_booked' => 'Appointment Booked',
            'appointment_cancelled' => 'Appointment Cancelled',
            'appointment_completed' => 'Appointment Completed',
            'schedule_created' => 'Schedule Created',
            'schedule_updated' => 'Schedule Updated',
            'feedback_submitted' => 'Feedback Submitted',
            default => ucfirst($this->action),
        };
    }

    public function getActionIcon(): string
    {
        return match($this->action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'login' => '🔐',
            'logout' => '🚪',
            'password_changed' => '🔑',
            'profile_updated' => '👤',
            'appointment_booked' => '📅',
            'appointment_cancelled' => '❌',
            'appointment_completed' => '✅',
            'schedule_created' => '⏰',
            'schedule_updated' => '🔄',
            'feedback_submitted' => '⭐',
            default => '📝',
        };
    }

    public function getActionColor(): string
    {
        return match($this->action) {
            'created' => 'text-green-600',
            'updated' => 'text-blue-600',
            'deleted' => 'text-red-600',
            'login' => 'text-green-600',
            'logout' => 'text-gray-600',
            'password_changed' => 'text-yellow-600',
            'profile_updated' => 'text-blue-600',
            'appointment_booked' => 'text-green-600',
            'appointment_cancelled' => 'text-red-600',
            'appointment_completed' => 'text-green-600',
            'schedule_created' => 'text-blue-600',
            'schedule_updated' => 'text-blue-600',
            'feedback_submitted' => 'text-yellow-600',
            default => 'text-gray-600',
        };
    }

    public function getModelLabel(): string
    {
        return match($this->model_type) {
            'App\Models\User' => 'User',
            'App\Models\Appointment' => 'Appointment',
            'App\Models\Schedule' => 'Schedule',
            'App\Models\FeedbackForm' => 'Feedback',
            'App\Models\PersonalDataSheet' => 'Personal Data Sheet',
            default => class_basename($this->model_type),
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
