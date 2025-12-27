<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    /**
     * Create a system backup and store it.
     *
     * @param string $type 'automatic' or 'manual'
     * @return array
     */
    public function createBackup(string $type = 'automatic'): array
    {
        $startTime = microtime(true);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "cgs_backup_{$timestamp}.json";
        
        try {
            // Collect all backup data
            $backupData = [
                'timestamp' => now()->toISOString(),
                'type' => $type,
                'version' => '1.0',
                'database' => [
                    'users' => DB::table('users')->get()->toArray(),
                    'appointments' => DB::table('appointments')->get()->toArray(),
                    'schedules' => DB::table('schedules')->get()->toArray(),
                    'notifications' => DB::table('notifications')->get()->toArray(),
                    'activity_logs' => DB::table('activity_logs')->get()->toArray(),
                    'audit_logs' => DB::table('audit_logs')->get()->toArray(),
                    'personal_data_sheets' => DB::table('personal_data_sheets')->get()->toArray(),
                    'feedback_forms' => DB::table('feedback_forms')->get()->toArray(),
                    'counselor_unavailable_dates' => DB::table('counselor_unavailable_dates')->get()->toArray(),
                    'authorized_ids' => DB::table('authorized_ids')->get()->toArray(),
                    'valid_ids' => DB::table('valid_ids')->get()->toArray(),
                ],
            ];

            // Encode to JSON
            $jsonData = json_encode($backupData, JSON_PRETTY_PRINT);
            $size = strlen($jsonData);

            // Ensure backups directory exists
            $backupsDir = storage_path('app/backups');
            if (!file_exists($backupsDir)) {
                mkdir($backupsDir, 0755, true);
            }

            // Store backup in storage/app/backups directory
            $backupPath = "backups/{$filename}";
            Storage::disk('local')->put($backupPath, $jsonData);

            // Log backup in database (if table exists)
            try {
                DB::table('backup_logs')->insert([
                    'filename' => $filename,
                    'path' => $backupPath,
                    'type' => $type,
                    'size' => $size,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Table might not exist yet, log but don't fail
                Log::warning('Could not log backup to database', ['error' => $e->getMessage()]);
            }

            $duration = round(microtime(true) - $startTime, 2);

            // Fire backup completed event
            event(new \App\Events\BackupCompleted($type, $backupPath, $size, $duration));

            Log::info("Backup created successfully", [
                'type' => $type,
                'filename' => $filename,
                'size' => $size,
                'duration' => $duration,
            ]);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $backupPath,
                'size' => $size,
                'duration' => $duration,
                'message' => "Backup created successfully: {$filename}",
            ];

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            
            // Log failed backup (if table exists)
            try {
                DB::table('backup_logs')->insert([
                    'filename' => $filename ?? 'failed_backup',
                    'path' => null,
                    'type' => $type,
                    'size' => 0,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $dbException) {
                // Table might not exist yet, log but don't fail
                Log::warning('Could not log failed backup to database', ['error' => $dbException->getMessage()]);
            }

            // Fire backup failed event
            event(new \App\Events\BackupFailed($type, $e->getMessage()));

            Log::error("Backup failed", [
                'type' => $type,
                'error' => $e->getMessage(),
                'duration' => $duration,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => "Backup failed: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Get the last backup time.
     *
     * @return string|null
     */
    public function getLastBackupTime(): ?string
    {
        try {
            $lastBackup = DB::table('backup_logs')
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->first();

            return $lastBackup ? Carbon::parse($lastBackup->created_at)->format('Y-m-d H:i:s') : null;
        } catch (\Exception $e) {
            // Table might not exist yet
            return null;
        }
    }

    /**
     * Get backup statistics.
     *
     * @return array
     */
    public function getBackupStats(): array
    {
        try {
            $totalBackups = DB::table('backup_logs')->where('status', 'completed')->count();
            $totalSize = DB::table('backup_logs')->where('status', 'completed')->sum('size') ?? 0;
            $lastBackup = $this->getLastBackupTime();
            $automaticBackups = DB::table('backup_logs')
                ->where('status', 'completed')
                ->where('type', 'automatic')
                ->count();
            $manualBackups = DB::table('backup_logs')
                ->where('status', 'completed')
                ->where('type', 'manual')
                ->count();

            return [
                'total_backups' => $totalBackups,
                'total_size' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'last_backup' => $lastBackup,
                'automatic_backups' => $automaticBackups,
                'manual_backups' => $manualBackups,
            ];
        } catch (\Exception $e) {
            // Table might not exist yet
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'total_size_mb' => 0,
                'last_backup' => null,
                'automatic_backups' => 0,
                'manual_backups' => 0,
            ];
        }
    }

    /**
     * Get recent backups.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentBackups(int $limit = 10)
    {
        try {
            return DB::table('backup_logs')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            // Table might not exist yet
            return collect([]);
        }
    }

    /**
     * Clean up old backups based on retention policy.
     *
     * @param int $daysToKeep Number of days to keep backups (default: 30)
     * @return array
     */
    public function cleanupOldBackups(int $daysToKeep = 30): array
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $oldBackups = DB::table('backup_logs')
            ->where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->get();

        $deletedCount = 0;
        $deletedSize = 0;

        foreach ($oldBackups as $backup) {
            try {
                // Delete file from storage
                if ($backup->path && Storage::disk('local')->exists($backup->path)) {
                    Storage::disk('local')->delete($backup->path);
                }

                // Delete log entry
                DB::table('backup_logs')->where('id', $backup->id)->delete();

                $deletedCount++;
                $deletedSize += $backup->size ?? 0;
            } catch (\Exception $e) {
                Log::error("Failed to delete old backup", [
                    'backup_id' => $backup->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'deleted_count' => $deletedCount,
            'deleted_size' => $deletedSize,
            'deleted_size_mb' => round($deletedSize / 1024 / 1024, 2),
        ];
    }

    /**
     * Download a backup file.
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function downloadBackup(string $filename)
    {
        $backup = DB::table('backup_logs')
            ->where('filename', $filename)
            ->where('status', 'completed')
            ->first();

        if (!$backup || !$backup->path) {
            return null;
        }

        if (!Storage::disk('local')->exists($backup->path)) {
            return null;
        }

        return Storage::disk('local')->download($backup->path, $backup->filename);
    }
}

