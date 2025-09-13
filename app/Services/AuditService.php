<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    /**
     * Log user action
     */
    public function logAction(string $action, array $data = [], ?User $user = null): void
    {
        try {
            $user = $user ?? Auth::user();
            
            $auditData = [
                'user_id' => $user?->id,
                'action' => $action,
                'data' => json_encode($data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'timestamp' => now(),
                'session_id' => session()->getId(),
                'request_id' => $this->generateRequestId(),
            ];

            // Store in database
            AuditLog::create($auditData);

            // Also log to file for backup
            Log::channel('audit')->info($action, $auditData);

        } catch (\Exception $e) {
            Log::error('Audit logging failed', [
                'action' => $action,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Log authentication events
     */
    public function logAuthEvent(string $event, array $data = []): void
    {
        $this->logAction("auth.{$event}", array_merge($data, [
            'email' => $data['email'] ?? 'unknown',
            'success' => $data['success'] ?? false,
            'failure_reason' => $data['failure_reason'] ?? null,
        ]));
    }

    /**
     * Log data access
     */
    public function logDataAccess(string $resource, string $action, array $data = []): void
    {
        $this->logAction("data.{$action}", array_merge($data, [
            'resource' => $resource,
            'resource_id' => $data['resource_id'] ?? null,
            'fields_accessed' => $data['fields_accessed'] ?? [],
        ]));
    }

    /**
     * Log data modification
     */
    public function logDataModification(string $resource, string $action, array $oldData = [], array $newData = []): void
    {
        $this->logAction("data.{$action}", [
            'resource' => $resource,
            'resource_id' => $newData['id'] ?? null,
            'old_data' => $oldData,
            'new_data' => $newData,
            'changes' => $this->getChanges($oldData, $newData),
        ]);
    }

    /**
     * Log file operations
     */
    public function logFileOperation(string $operation, string $filename, array $data = []): void
    {
        $this->logAction("file.{$operation}", array_merge($data, [
            'filename' => $filename,
            'file_size' => $data['file_size'] ?? null,
            'file_type' => $data['file_type'] ?? null,
            'upload_path' => $data['upload_path'] ?? null,
        ]));
    }

    /**
     * Log system events
     */
    public function logSystemEvent(string $event, array $data = []): void
    {
        $this->logAction("system.{$event}", array_merge($data, [
            'server_info' => [
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
                'load_average' => sys_getloadavg(),
            ],
        ]));
    }

    /**
     * Log security events
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        $this->logAction("security.{$event}", array_merge($data, [
            'risk_level' => $data['risk_level'] ?? 'medium',
            'threat_type' => $data['threat_type'] ?? 'unknown',
            'mitigation_taken' => $data['mitigation_taken'] ?? null,
        ]));
    }

    /**
     * Log API usage
     */
    public function logApiUsage(string $endpoint, array $data = []): void
    {
        $this->logAction("api.usage", array_merge($data, [
            'endpoint' => $endpoint,
            'response_time' => $data['response_time'] ?? null,
            'status_code' => $data['status_code'] ?? null,
            'request_size' => strlen(json_encode($data['request_data'] ?? [])),
            'response_size' => strlen(json_encode($data['response_data'] ?? [])),
        ]));
    }

    /**
     * Get audit trail for a specific resource
     */
    public function getAuditTrail(string $resource, int $resourceId, int $limit = 50): array
    {
        return AuditLog::where('data->resource', $resource)
            ->where('data->resource_id', $resourceId)
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary(int $userId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $activities = AuditLog::where('user_id', $userId)
            ->where('timestamp', '>=', $startDate)
            ->get();

        $summary = [
            'total_actions' => $activities->count(),
            'actions_by_type' => $activities->groupBy('action')->map->count(),
            'last_activity' => $activities->max('timestamp'),
            'most_used_features' => $activities->groupBy('action')->map->count()->sortDesc()->take(5),
            'daily_activity' => $activities->groupBy(function($activity) {
                return $activity->timestamp->format('Y-m-d');
            })->map->count(),
        ];

        return $summary;
    }

    /**
     * Get system activity summary
     */
    public function getSystemActivitySummary(int $days = 7): array
    {
        $startDate = now()->subDays($days);
        
        $activities = AuditLog::where('timestamp', '>=', $startDate)->get();

        $summary = [
            'total_actions' => $activities->count(),
            'unique_users' => $activities->unique('user_id')->count(),
            'actions_by_type' => $activities->groupBy('action')->map->count(),
            'hourly_distribution' => $activities->groupBy(function($activity) {
                return $activity->timestamp->format('H');
            })->map->count(),
            'daily_distribution' => $activities->groupBy(function($activity) {
                return $activity->timestamp->format('Y-m-d');
            })->map->count(),
            'top_users' => $activities->groupBy('user_id')->map->count()->sortDesc()->take(10),
        ];

        return $summary;
    }

    /**
     * Search audit logs
     */
    public function searchAuditLogs(array $filters = [], int $limit = 100): array
    {
        $query = AuditLog::query();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', 'like', "%{$filters['action']}%");
        }

        if (isset($filters['start_date'])) {
            $query->where('timestamp', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('timestamp', '<=', $filters['end_date']);
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        return $query->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Export audit logs
     */
    public function exportAuditLogs(array $filters = [], string $format = 'csv'): string
    {
        $logs = $this->searchAuditLogs($filters, 10000);

        if ($format === 'csv') {
            return $this->exportToCsv($logs);
        } elseif ($format === 'json') {
            return json_encode($logs, JSON_PRETTY_PRINT);
        }

        throw new \InvalidArgumentException('Unsupported export format');
    }

    /**
     * Clean old audit logs
     */
    public function cleanOldAuditLogs(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $deletedCount = AuditLog::where('timestamp', '<', $cutoffDate)->delete();
        
        Log::info('Cleaned old audit logs', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate,
        ]);

        return $deletedCount;
    }

    /**
     * Generate unique request ID
     */
    protected function generateRequestId(): string
    {
        return uniqid('req_', true);
    }

    /**
     * Get changes between old and new data
     */
    protected function getChanges(array $oldData, array $newData): array
    {
        $changes = [];
        
        foreach ($newData as $key => $newValue) {
            if (isset($oldData[$key]) && $oldData[$key] !== $newValue) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Export to CSV format
     */
    protected function exportToCsv(array $logs): string
    {
        if (empty($logs)) {
            return '';
        }

        $headers = array_keys($logs[0]);
        $csv = fopen('php://temp', 'r+');
        
        fputcsv($csv, $headers);
        
        foreach ($logs as $log) {
            fputcsv($csv, array_values($log));
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return $content;
    }

    /**
     * Monitor for suspicious activity
     */
    public function monitorSuspiciousActivity(): array
    {
        $suspiciousActivities = [];
        
        // Check for rapid successive actions
        $recentActions = AuditLog::where('timestamp', '>=', now()->subMinutes(5))
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id');

        foreach ($recentActions as $userId => $actions) {
            if ($actions->count() > 50) { // More than 50 actions in 5 minutes
                $suspiciousActivities[] = [
                    'type' => 'rapid_actions',
                    'user_id' => $userId,
                    'action_count' => $actions->count(),
                    'timeframe' => '5 minutes',
                    'risk_level' => 'high',
                ];
            }
        }

        // Check for unusual access patterns
        $unusualAccess = AuditLog::where('timestamp', '>=', now()->subHour())
            ->where('action', 'like', 'data.%')
            ->get()
            ->groupBy('user_id');

        foreach ($unusualAccess as $userId => $actions) {
            $uniqueResources = $actions->pluck('data->resource')->unique()->count();
            if ($uniqueResources > 20) { // Accessing more than 20 different resources in an hour
                $suspiciousActivities[] = [
                    'type' => 'unusual_access',
                    'user_id' => $userId,
                    'resources_accessed' => $uniqueResources,
                    'timeframe' => '1 hour',
                    'risk_level' => 'medium',
                ];
            }
        }

        return $suspiciousActivities;
    }

    /**
     * Get compliance report
     */
    public function getComplianceReport(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $activities = AuditLog::where('timestamp', '>=', $startDate)->get();

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => now(),
                'days' => $days,
            ],
            'summary' => [
                'total_actions' => $activities->count(),
                'unique_users' => $activities->unique('user_id')->count(),
                'unique_ips' => $activities->unique('ip_address')->count(),
            ],
            'data_access' => [
                'total_accesses' => $activities->where('action', 'like', 'data.%')->count(),
                'accesses_by_resource' => $activities->where('action', 'like', 'data.%')
                    ->groupBy('data->resource')->map->count(),
            ],
            'security_events' => [
                'total_events' => $activities->where('action', 'like', 'security.%')->count(),
                'events_by_type' => $activities->where('action', 'like', 'security.%')
                    ->groupBy('action')->map->count(),
            ],
            'authentication' => [
                'total_logins' => $activities->where('action', 'auth.login')->count(),
                'failed_logins' => $activities->where('action', 'auth.failed')->count(),
                'password_resets' => $activities->where('action', 'auth.password_reset')->count(),
            ],
        ];

        return $report;
    }
}
