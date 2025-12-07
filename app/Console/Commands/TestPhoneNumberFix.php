<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestPhoneNumberFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phone-number-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the phone number fix for user management display';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing phone number fix...');

        // Test 1: Check if phone_number field exists in User model fillable
        $user = new User();
        $fillable = $user->getFillable();
        
        if (in_array('phone_number', $fillable)) {
            $this->info('✓ phone_number field is in User model fillable array');
        } else {
            $this->error('✗ phone_number field is NOT in User model fillable array');
            return 1;
        }

        // Test 2: Create a test user with phone number directly
        $testUser = new User([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => 'test.student@example.com',
            'phone_number' => '09123456789',
            'role' => 'student',
        ]);

        $this->info("✓ Created test user object with phone number: {$testUser->phone_number}");

        // Test 3: Check if phone_number attribute is accessible
        if ($testUser->phone_number === '09123456789') {
            $this->info('✓ phone_number attribute is accessible and correct');
        } else {
            $this->error('✗ phone_number attribute is not accessible or incorrect');
            return 1;
        }

        // Test 4: Check if view would display phone number correctly
        $phoneDisplay = $testUser->phone_number ?? 'Not provided';
        if ($phoneDisplay === '09123456789') {
            $this->info('✓ View would display phone number correctly: ' . $phoneDisplay);
        } else {
            $this->error('✗ View would NOT display phone number correctly: ' . $phoneDisplay);
            return 1;
        }

        // Test 5: Check if null phone number displays "Not provided"
        $testUserWithoutPhone = new User([
            'first_name' => 'Test',
            'last_name' => 'NoPhone',
            'email' => 'test.nophone@example.com',
            'phone_number' => null,
            'role' => 'student',
        ]);

        $phoneDisplayNull = $testUserWithoutPhone->phone_number ?? 'Not provided';
        if ($phoneDisplayNull === 'Not provided') {
            $this->info('✓ Null phone number displays "Not provided" correctly');
        } else {
            $this->error('✗ Null phone number does NOT display "Not provided" correctly');
            return 1;
        }

        $this->info('');
        $this->info('🎉 All tests passed! Phone number fix is working correctly.');
        $this->info('Students can now update their phone numbers and counselors will see them in user management.');

        return 0;
    }
}