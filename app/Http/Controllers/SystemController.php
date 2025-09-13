<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('counselor_only');
    }

    /**
     * Display system backup information.
     */
    public function backup(): View
    {
        $backupInfo = [
            'database_size' => $this->getDatabaseSize(),
            'last_backup' => $this->getLastBackupTime(),
            'total_users' => DB::table('users')->count(),
            'total_appointments' => DB::table('appointments')->count(),
            'total_activity_logs' => DB::table('activity_logs')->count(),
            'system_uptime' => $this->getSystemUptime(),
        ];

        return view('system.backup', compact('backupInfo'));
    }

    /**
     * Download system backup.
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
     * Get last backup time.
     */
    private function getLastBackupTime(): string
    {
        // This would typically check a backup log or storage
        return 'Never';
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
