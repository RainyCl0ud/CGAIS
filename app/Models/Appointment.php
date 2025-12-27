<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'counselor_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'type',
        'counseling_category',
        'reason',
        'notes',
        'counselor_notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', 'no_show');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRescheduled($query)
    {
        return $query->where('status', 'rescheduled');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeForCounselor($query, $counselorId)
    {
        return $query->where('counselor_id', $counselorId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                    ->where('status', '!=', 'cancelled');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isNoShow(): bool
    {
        return $this->status === 'no_show';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isRescheduled(): bool
    {
        return $this->status === 'rescheduled';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isOnHold(): bool
    {
        return $this->status === 'on_hold';
    }

    public function isUrgent(): bool
    {
        return $this->type === 'urgent';
    }

    public function getAppointmentDateTime(): Carbon
    {
        return Carbon::parse($this->appointment_date)->setTimeFrom($this->start_time);
    }

    public function getFormattedDateTime(): string
    {
        return $this->appointment_date->format('M d, Y') . ' at ' . 
               $this->start_time->format('g:i A') . ' - ' . 
               $this->end_time->format('g:i A');
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
            'rejected' => 'bg-red-100 text-red-800',
            'rescheduled' => 'bg-purple-100 text-purple-800',
            'failed' => 'bg-red-100 text-red-800',
            'on_hold' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            'regular' => 'bg-blue-100 text-blue-800',
            'urgent' => 'bg-red-100 text-red-800',
            'follow_up' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'regular' => 'Consultation',
            'urgent' => 'Referral',
            'follow_up' => 'Consultation',
            default => ucfirst($this->type),
        };
    }

    public function getCounselingCategoryLabel(): string
    {
        return match($this->counseling_category) {
            'conduct_intake_interview' => 'Conduct Intake Interview',
            'information_services' => 'Information Services',
            'internal_referral_services' => 'Internal Referral Services',
            'counseling_services' => 'Counseling Services',
            'conduct_exit_interview' => 'Conduct Exit Interview',
            'consultation' => 'Consultation',
            default => 'Not specified'
        };
    }

    public function getCounselingCategoryBadgeClass(): string
    {
        return match($this->counseling_category) {
            'conduct_intake_interview' => 'bg-blue-100 text-blue-800',
            'information_services' => 'bg-green-100 text-green-800',
            'internal_referral_services' => 'bg-yellow-100 text-yellow-800',
            'counseling_services' => 'bg-purple-100 text-purple-800',
            'conduct_exit_interview' => 'bg-indigo-100 text-indigo-800',
            'consultation' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Alias methods for backward compatibility
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->getFormattedDateTime();
    }

    public function getTypeBadgeClasses(): string
    {
        return $this->getTypeBadgeClass();
    }

    public function getStatusBadgeClasses(): string
    {
        return $this->getStatusBadgeClass();
    }

    public function isOverdue(): bool
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s'));
        return $this->isPending() && $appointmentDateTime->lt(now());
    }
}
