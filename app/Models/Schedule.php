<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'counselor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'max_appointments',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    // Relationships
    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    public function scopeForCounselor($query, $counselorId)
    {
        return $query->where('counselor_id', $counselorId);
    }

    // Helper methods
    public function getAvailableSlots($date)
    {
        $bookedAppointments = Appointment::where('counselor_id', $this->counselor_id)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();

        return max(0, $this->max_appointments - $bookedAppointments);
    }

    public function getFormattedTime(): string
    {
        return $this->start_time->format('g:i A') . ' - ' . $this->end_time->format('g:i A');
    }

    public function getDayName(): string
    {
        return ucfirst($this->day_of_week);
    }
} 