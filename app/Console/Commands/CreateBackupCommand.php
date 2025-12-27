<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class CreateBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--type=automatic : Type of backup (automatic or manual)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a system backup';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type');
        
        if (!in_array($type, ['automatic', 'manual'])) {
            $this->error('Invalid backup type. Use "automatic" or "manual".');
            return Command::FAILURE;
        }

        $this->info("Creating {$type} backup...");

        $result = $backupService->createBackup($type);

        if ($result['success']) {
            $this->info("✓ Backup created successfully!");
            $this->info("  Filename: {$result['filename']}");
            $this->info("  Size: " . round($result['size'] / 1024 / 1024, 2) . " MB");
            $this->info("  Duration: {$result['duration']} seconds");
            
            // Cleanup old backups (keep last 30 days)
            $this->info("Cleaning up old backups...");
            $cleanupResult = $backupService->cleanupOldBackups(30);
            if ($cleanupResult['deleted_count'] > 0) {
                $this->info("  Deleted {$cleanupResult['deleted_count']} old backup(s)");
                $this->info("  Freed " . $cleanupResult['deleted_size_mb'] . " MB");
            }
            
            return Command::SUCCESS;
        } else {
            $this->error("✗ Backup failed: {$result['error']}");
            return Command::FAILURE;
        }
    }
}



