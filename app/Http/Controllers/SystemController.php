<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Services\BackupService;
use Carbon\Carbon;

class SystemController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->middleware('counselor_only');
        $this->backupService = $backupService;
    }

    /**
     * Display system backup information.
     */
    public function backup(): View
    {
        $backupStats = $this->backupService->getBackupStats();
        $recentBackups = $this->backupService->getRecentBackups(10);

        $backupInfo = [
            'database_size' => $this->getDatabaseSize(),
            'last_backup' => $backupStats['last_backup'] ?? 'Never',
            'total_users' => DB::table('users')->count(),
            'total_appointments' => DB::table('appointments')->count(),
            'total_activity_logs' => DB::table('activity_logs')->count(),
            'system_uptime' => $this->getSystemUptime(),
            'backup_stats' => $backupStats,
            'recent_backups' => $recentBackups,
        ];

        return view('system.backup', compact('backupInfo'));
    }

    /**
     * Create a manual backup.
     */
    public function createManualBackup(): Response
    {
        $result = $this->backupService->createBackup('manual');

        if ($result['success']) {
            return redirect()->route('system.backup')
                ->with('success', "Manual backup created successfully: {$result['filename']}");
        } else {
            return redirect()->route('system.backup')
                ->with('error', "Backup failed: {$result['error']}");
        }
    }

    /**
     * Download a specific backup file.
     */
    public function downloadBackupFile(string $filename)
    {
        $download = $this->backupService->downloadBackup($filename);

        if ($download) {
            return $download;
        }

        return redirect()->route('system.backup')
            ->with('error', 'Backup file not found.');
    }

    /**
     * Download system backup (legacy method - creates on-the-fly backup).
     */
    public function downloadBackup(): Response
    {
        $backupData = [
            'timestamp' => now()->toISOString(),
            'users' => DB::table('users')->get(),
            'appointments' => DB::table('appointments')->get(),
            'schedules' => DB::table('schedules')->get(),
            'notifications' => DB::table('notifications')->get(),
            'activity_logs' => DB::table('activity_logs')->get(),
            'audit_logs' => DB::table('audit_logs')->get(),
            'personal_data_sheets' => DB::table('personal_data_sheets')->get(),
            'feedback_forms' => DB::table('feedback_forms')->get(),
        ];

        $filename = 'cgs_backup_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response(json_encode($backupData, JSON_PRETTY_PRINT), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get database size in MB.
     */
    private function getDatabaseSize(): string
    {
        try {
            $size = DB::select('SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "size" FROM information_schema.TABLES WHERE table_schema = ?', [config('database.connections.mysql.database')]);
            return $size[0]->size . ' MB';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }


    /**
     * Get system uptime.
     */
    private function getSystemUptime(): string
    {
        try {
            $uptime = DB::select('SELECT NOW() - INTERVAL 1 DAY as uptime')[0]->uptime;
            return Carbon::parse($uptime)->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
