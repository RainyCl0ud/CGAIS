<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AuditLog extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'data',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'timestamp',
        'session_id',
        'request_id',
        'risk_level',
        'threat_type',
        'mitigation_taken',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'timestamp' => 'datetime',
        'risk_level' => 'string',
        'threat_type' => 'string',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'session_id',
        'request_id',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include actions by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include actions of a specific type.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', 'like', "%{$action}%");
    }

    /**
     * Scope a query to only include actions within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include actions from a specific IP address.
     */
    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope a query to only include high-risk actions.
     */
    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high');
    }

    /**
     * Scope a query to only include security-related actions.
     */
    public function scopeSecurityEvents($query)
    {
        return $query->where('action', 'like', 'security.%');
    }

    /**
     * Scope a query to only include authentication events.
     */
    public function scopeAuthEvents($query)
    {
        return $query->where('action', 'like', 'auth.%');
    }

    /**
     * Scope a query to only include data access events.
     */
    public function scopeDataEvents($query)
    {
        return $query->where('action', 'like', 'data.%');
    }

    /**
     * Get the action type (auth, data, security, etc.)
     */
    public function getActionTypeAttribute()
    {
        $parts = explode('.', $this->action);
        return $parts[0] ?? 'unknown';
    }

    /**
     * Get the specific action (login, create, update, etc.)
     */
    public function getSpecificActionAttribute()
    {
        $parts = explode('.', $this->action);
        return $parts[1] ?? 'unknown';
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimestampAttribute()
    {
        return $this->timestamp->format('Y-m-d H:i:s');
    }

    /**
     * Get human-readable action description
     */
    public function getActionDescriptionAttribute()
    {
        $descriptions = [
            'auth.login' => 'User logged in',
            'auth.logout' => 'User logged out',
            'auth.failed' => 'Failed login attempt',
            'auth.password_reset' => 'Password reset',
            'data.create' => 'Data created',
            'data.update' => 'Data updated',
            'data.delete' => 'Data deleted',
            'data.view' => 'Data viewed',
            'security.suspicious_activity' => 'Suspicious activity detected',
            'security.brute_force' => 'Brute force attack detected',
            'security.unauthorized_access' => 'Unauthorized access attempt',
            'file.upload' => 'File uploaded',
            'file.download' => 'File downloaded',
            'file.delete' => 'File deleted',
            'system.maintenance' => 'System maintenance',
            'system.backup' => 'System backup',
            'api.usage' => 'API accessed',
        ];

        return $descriptions[$this->action] ?? 'Unknown action';
    }

    /**
     * Get risk level color for UI
     */
    public function getRiskLevelColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            'critical' => 'purple',
        ];

        return $colors[$this->risk_level] ?? 'gray';
    }

    /**
     * Check if this is a security event
     */
    public function getIsSecurityEventAttribute()
    {
        return str_starts_with($this->action, 'security.');
    }

    /**
     * Check if this is an authentication event
     */
    public function getIsAuthEventAttribute()
    {
        return str_starts_with($this->action, 'auth.');
    }

    /**
     * Check if this is a data access event
     */
    public function getIsDataEventAttribute()
    {
        return str_starts_with($this->action, 'data.');
    }

    /**
     * Get resource name from data
     */
    public function getResourceNameAttribute()
    {
        return $this->data['resource'] ?? null;
    }

    /**
     * Get resource ID from data
     */
    public function getResourceIdAttribute()
    {
        return $this->data['resource_id'] ?? null;
    }

    /**
     * Get changes made in this action
     */
    public function getChangesAttribute()
    {
        return $this->data['changes'] ?? [];
    }

    /**
     * Get old data from this action
     */
    public function getOldDataAttribute()
    {
        return $this->data['old_data'] ?? [];
    }

    /**
     * Get new data from this action
     */
    public function getNewDataAttribute()
    {
        return $this->data['new_data'] ?? [];
    }

    /**
     * Get user email (for display purposes)
     */
    public function getUserEmailAttribute()
    {
        return $this->user?->email ?? 'Unknown User';
    }

    /**
     * Get user name (for display purposes)
     */
    public function getUserNameAttribute()
    {
        return $this->user?->full_name ?? 'Unknown User';
    }

    /**
     * Get location from IP address (if available)
     */
    public function getLocationAttribute()
    {
        // This would typically integrate with a geolocation service
        // For now, return null
        return null;
    }

    /**
     * Get browser information from user agent
     */
    public function getBrowserInfoAttribute()
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        // Simple browser detection
        if (strpos($this->user_agent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($this->user_agent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($this->user_agent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($this->user_agent, 'Edge') !== false) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceTypeAttribute()
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        if (strpos($this->user_agent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($this->user_agent, 'Tablet') !== false) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Check if this action was performed recently
     */
    public function getIsRecentAttribute()
    {
        return $this->timestamp->isAfter(now()->subMinutes(5));
    }

    /**
     * Check if this action was performed today
     */
    public function getIsTodayAttribute()
    {
        return $this->timestamp->isToday();
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute()
    {
        return $this->timestamp->diffForHumans();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-clean old logs (older than 1 year)
        static::creating(function ($auditLog) {
            // Clean old logs if we have too many
            $count = static::count();
            if ($count > 100000) { // Keep max 100k logs
                $oldestLogs = static::orderBy('timestamp', 'asc')
                    ->limit(1000)
                    ->pluck('id');
                
                static::whereIn('id', $oldestLogs)->delete();
            }
        });
    }
}
