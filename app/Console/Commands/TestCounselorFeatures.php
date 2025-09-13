<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestCounselorFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:counselor {--create-data : Create test data if none exists} {--cleanup : Clean up test data after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all counselor features and display results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Counselor Feature Testing ===');
        
        // 1. Test Counselor Authentication
        $this->testCounselorAuthentication();
        
        // 2. Test Schedule Management
        $this->testScheduleManagement();
        
        // 3. Test Appointment Management
        $this->testAppointmentManagement();
        
        // 4. Test Dashboard Statistics
        $this->testDashboardStatistics();
        
        // 5. Test Notification System
        $this->testNotificationSystem();
        
        // 6. Test Role-Based Access
        $this->testRoleBasedAccess();
        
        // 7. Create test data if requested
        if ($this->option('create-data')) {
            $this->createTestData();
        }
        
        // 8. Cleanup if requested
        if ($this->option('cleanup')) {
            $this->cleanupTestData();
        }
        
        $this->info('=== Testing Complete ===');
        $this->displayLoginCredentials();
    }
    
    private function testCounselorAuthentication()
    {
        $this->info('1. Testing Counselor Authentication...');
        
        $counselor = User::where('role', 'counselor')->first();
        
        if (!$counselor) {
            $this->error('❌ No counselor found! Run: php artisan db:seed');
            return;
        }
        
        $this->info("✅ Counselor found: {$counselor->full_name}");
        $this->info("✅ Email: {$counselor->email}");
        $this->info("✅ Role check: " . ($counselor->isCounselor() ? 'PASS' : 'FAIL'));
    }
    
    private function testScheduleManagement()
    {
        $this->info('2. Testing Schedule Management...');
        
        $counselor = User::where('role', 'counselor')->first();
        $schedules = $counselor->schedules;
        
        $this->info("✅ Schedules: {$schedules->count()} found");
        
        if ($schedules->count() > 0) {
            $this->info("✅ Available schedules: " . $schedules->where('is_available', true)->count());
        }
    }
    
    private function testAppointmentManagement()
    {
        $this->info('3. Testing Appointment Management...');
        
        $counselor = User::where('role', 'counselor')->first();
        $appointments = $counselor->counselorAppointments;
        
        $this->info("✅ Appointments: {$appointments->count()} found");
        $this->info("✅ Pending appointments: " . $appointments->where('status', 'pending')->count());
        $this->info("✅ Confirmed appointments: " . $appointments->where('status', 'confirmed')->count());
    }
    
    private function testDashboardStatistics()
    {
        $this->info('4. Testing Dashboard Statistics...');
        
        $counselor = User::where('role', 'counselor')->first();
        
        $totalAppointments = $counselor->counselorAppointments()->count();
        $pendingAppointments = $counselor->counselorAppointments()->where('status', 'pending')->count();
        $todayAppointments = $counselor->counselorAppointments()
            ->where('appointment_date', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->count();
        
        $this->info("✅ Total appointments: {$totalAppointments}");
        $this->info("✅ Pending appointments: {$pendingAppointments}");
        $this->info("✅ Today's appointments: {$todayAppointments}");
    }
    
    private function testNotificationSystem()
    {
        $this->info('5. Testing Notification System...');
        
        $counselor = User::where('role', 'counselor')->first();
        $notifications = $counselor->notifications;
        $unreadCount = $counselor->getUnreadNotificationsCount();
        
        $this->info("✅ Notifications: {$notifications->count()} found");
        $this->info("✅ Unread notifications: {$unreadCount}");
    }
    
    private function testRoleBasedAccess()
    {
        $this->info('6. Testing Role-Based Access...');
        
        $roles = ['student', 'faculty', 'counselor', 'assistant'];
        
        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            if ($user) {
                $method = 'is' . ucfirst($role);
                $result = $user->$method() ? 'PASS' : 'FAIL';
                $this->info("✅ {$role} role check: {$result}");
            }
        }
    }
    
    private function createTestData()
    {
        $this->info('7. Creating Test Data...');
        
        $counselor = User::where('role', 'counselor')->first();
        $student = User::where('role', 'student')->first();
        
        if (!$student) {
            $this->error('❌ No student found for creating test appointments');
            return;
        }
        
        // Create test appointment
        $appointment = Appointment::create([
            'user_id' => $student->id,
            'counselor_id' => $counselor->id,
            'appointment_date' => now()->addDays(1),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'type' => 'regular',
            'reason' => 'Test appointment created by command',
            'status' => 'pending',
        ]);
        
        $this->info("✅ Created test appointment #{$appointment->id}");
        
        // Create test notification
        $notification = Notification::create([
            'user_id' => $counselor->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification created by command',
            'type' => 'appointment',
            'is_read' => false,
        ]);
        
        $this->info("✅ Created test notification #{$notification->id}");
    }
    
    private function cleanupTestData()
    {
        $this->info('8. Cleaning Up Test Data...');
        
        $deletedAppointments = Appointment::where('reason', 'Test appointment created by command')->delete();
        $deletedNotifications = Notification::where('title', 'Test Notification')->delete();
        
        $this->info("✅ Deleted {$deletedAppointments} test appointments");
        $this->info("✅ Deleted {$deletedNotifications} test notifications");
    }
    
    private function displayLoginCredentials()
    {
        $counselor = User::where('role', 'counselor')->first();
        
        $this->info('');
        $this->info('=== Login Credentials ===');
        $this->info("Email: {$counselor->email}");
        $this->info('Password: admin');
        $this->info('');
        $this->info('=== Test URLs ===');
        $this->info('- Dashboard: /dashboard');
        $this->info('- Appointments: /appointments');
        $this->info('- Schedules: /schedules');
        $this->info('- Notifications: /notifications');
    }
} 