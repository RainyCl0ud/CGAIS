<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getTypeIcon(): string
    {
        return match($this->type) {
            'appointment_reminder' => '🔔',
            'appointment_confirmed' => '✅',
            'appointment_cancelled' => '❌',
            'general' => '📢',
            default => '📢',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            'appointment_reminder' => 'bg-blue-100 text-blue-800',
            'appointment_confirmed' => 'bg-green-100 text-green-800',
            'appointment_cancelled' => 'bg-red-100 text-red-800',
            'general' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
} 