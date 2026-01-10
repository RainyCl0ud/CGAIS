<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizedId extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_number',
        'type',
        'is_used',
        'registered_by',
        'used_by',
        'used_at',
        'notes',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user who used this ID
     */
    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Scope for available (unused) IDs
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope for used IDs
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Scope for student IDs
     */
    public function scopeStudent($query)
    {
        return $query->where('type', 'student');
    }

    /**
     * Scope for faculty IDs
     */
    public function scopeFaculty($query)
    {
        return $query->where('type', 'faculty');
    }

    /**
     * Scope for staff IDs
     */
    public function scopeStaff($query)
    {
        return $query->where('type', 'staff');
    }

    /**
     * Mark ID as used
     */
    public function markAsUsed(int $userId): void
    {
        if ($this->is_used) {
            throw new \Exception('This ID has already been used.');
        }
        
        $this->update([
            'is_used' => true,
            'used_by' => $userId,
            'used_at' => now(),
        ]);
    }

    /**
     * Check if ID is available
     */
    public function isAvailable(): bool
    {
        return !$this->is_used;
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedCreatedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y \a\t H:i');
    }

    /**
     * Get formatted used date
     */
    public function getFormattedUsedDateAttribute(): string
    {
        return $this->used_at ? $this->used_at->format('M d, Y \a\t H:i') : 'N/A';
    }

    /**
     * Check if ID can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_used;
    }

    /**
     * Get formatted type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'student' => 'Student',
            'faculty' => 'Faculty',
            'staff' => 'Non-Teaching Staff',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_used 
            ? 'bg-red-100 text-red-800' 
            : 'bg-green-100 text-green-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_used ? 'Used' : 'Available';
    }
}
