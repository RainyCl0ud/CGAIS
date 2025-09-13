<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateSampleActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-logs:create-sample {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample activity logs for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 2; // Default to user ID 2
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Creating sample activity logs for user: {$user->name}");

        $activities = [
            [
                'action' => 'login',
                'description' => 'Logged in to the system',
                'created_at' => now()->subHours(2),
            ],
            [
                'action' => 'appointment_booked',
                'description' => 'Booked appointment for Jan 15, 2025 at 10:00 AM',
                'created_at' => now()->subDays(1),
            ],
            [
                'action' => 'profile_updated',
                'description' => 'Updated profile information',
                'created_at' => now()->subDays(2),
            ],
            [
                'action' => 'appointment_cancelled',
                'description' => 'Cancelled appointment for Jan 10, 2025 at 2:00 PM',
                'created_at' => now()->subDays(3),
            ],
            [
                'action' => 'feedback_submitted',
                'description' => 'Submitted feedback for appointment on Jan 5, 2025',
                'created_at' => now()->subDays(4),
            ],
            [
                'action' => 'login',
                'description' => 'Logged in to the system',
                'created_at' => now()->subDays(5),
            ],
            [
                'action' => 'appointment_booked',
                'description' => 'Booked appointment for Jan 20, 2025 at 3:00 PM',
                'created_at' => now()->subDays(6),
            ],
            [
                'action' => 'password_changed',
                'description' => 'Changed password',
                'created_at' => now()->subDays(7),
            ],
            [
                'action' => 'logout',
                'description' => 'Logged out of the system',
                'created_at' => now()->subDays(8),
            ],
            [
                'action' => 'appointment_completed',
                'description' => 'Completed appointment for Dec 30, 2024 at 11:00 AM',
                'created_at' => now()->subDays(9),
            ],
        ];

        foreach ($activities as $activity) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $activity['action'],
                'description' => $activity['description'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => $activity['created_at'],
                'updated_at' => $activity['created_at'],
            ]);
        }

        $this->info("Created " . count($activities) . " sample activity logs!");
        return 0;
    }
}
