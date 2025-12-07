<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PendingEmailChangeNotification;
use Illuminate\Console\Command;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        
        $this->info("Testing email configuration...");
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("Mail host: " . config('mail.mailers.smtp.host'));
        $this->info("Mail port: " . config('mail.mailers.smtp.port'));
        $this->info("From address: " . config('mail.from.address'));
        
        try {
            $user = User::first();
            
            if (!$user) {
                $this->error('No users found in database.');
                return 1;
            }
            
            $this->info("Sending test email to: {$email}");
            
            $notification = new PendingEmailChangeNotification($email, 'https://example.com/verify');
            $user->notify($notification);
            
            $this->info("✅ Email test completed successfully!");
            $this->info("Check your email inbox (or logs if using 'log' mailer).");
            
        } catch (\Exception $e) {
            $this->error("❌ Email test failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
