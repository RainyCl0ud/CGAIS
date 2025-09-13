# Counselor Features Testing Guide

## Overview
This guide covers how to test all counselor features in the CGS system using various methods including Tinker, DD debugging, and manual testing.

## Counselor Features to Test

### 1. **Dashboard Features**
- View appointment statistics
- View recent appointments
- View upcoming appointments
- View unread notifications count

### 2. **Schedule Management**
- Create new schedules
- Edit existing schedules
- Delete schedules
- Set availability status
- Set maximum appointments per day

### 3. **Appointment Management**
- View assigned appointments
- Update appointment status
- Add counselor notes
- Filter appointments by status

### 4. **Notification System**
- View notifications
- Mark notifications as read
- Delete notifications

## Testing Methods

### Method 1: Using Laravel Tinker (Recommended)

#### Step 1: Start Tinker
```bash
php artisan tinker
```

#### Step 2: Run the Comprehensive Test Script
Copy and paste the entire content of `test_counselor_features.php` into Tinker.

#### Step 3: Verify Test Results
The script will output:
- ✓ Counselor authentication status
- ✓ Schedule management functionality
- ✓ Appointment creation and management
- ✓ Dashboard statistics
- ✓ Notification system
- ✓ Role-based access control

### Method 2: Using DD (Dump and Die) for Quick Debugging

#### Option A: Add to Controller Methods
Add any of these DD statements to your controller methods:

```php
// Test counselor data
$counselor = \App\Models\User::where('role', 'counselor')->first();
dd([
    'counselor' => $counselor,
    'schedules' => $counselor->schedules,
    'appointments' => $counselor->counselorAppointments,
    'notifications' => $counselor->notifications,
]);
```

#### Option B: Run Individual DD Tests
Copy individual sections from `dd_test_counselor.php` into Tinker.

### Method 3: Manual Testing with Browser

#### Step 1: Login as Counselor
- URL: `http://your-app.test/login`
- Email: `counselor@example.com`
- Password: `counselorpass`

#### Step 2: Test Each Feature

**Dashboard Testing:**
1. Navigate to `/dashboard`
2. Verify statistics are displayed correctly
3. Check recent appointments list
4. Verify upcoming appointments

**Schedule Management Testing:**
1. Navigate to `/schedules`
2. Create a new schedule
3. Edit an existing schedule
4. Delete a schedule
5. Toggle availability status

**Appointment Management Testing:**
1. Navigate to `/appointments`
2. View assigned appointments
3. Click on an appointment to view details
4. Update appointment status
5. Add counselor notes

**Notification Testing:**
1. Navigate to `/notifications`
2. View notification list
3. Mark notifications as read
4. Delete notifications

## Test Data Setup

### Existing Test Data
The system comes with pre-seeded test data:

**Counselor Account:**
- Email: `counselor@example.com`
- Password: `counselorpass`
- Name: Dr. Karren Elizabeth Johnson

**Student Account:**
- Email: `student@example.com`
- Password: `password`
- Name: John Michael Doe Jr.

**Assistant Account:**
- Email: `assistant@example.com`
- Password: `assistantpass`
- Name: Maria Clara Santos

### Creating Additional Test Data

#### Using Tinker:
```php
// Create additional counselor
$newCounselor = \App\Models\User::create([
    'first_name' => 'Dr. Sarah',
    'last_name' => 'Smith',
    'email' => 'sarah.counselor@example.com',
    'role' => 'counselor',
    'faculty_id' => 'F20240004',
    'password' => bcrypt('password'),
]);

// Create test appointments
$appointment = \App\Models\Appointment::create([
    'user_id' => 1, // student ID
    'counselor_id' => $newCounselor->id,
    'appointment_date' => now()->addDays(2),
    'start_time' => '14:00',
    'end_time' => '15:00',
    'type' => 'regular',
    'reason' => 'Academic counseling',
    'status' => 'pending',
]);
```

#### Using Database Seeder:
```bash
php artisan db:seed
```

## Common Testing Scenarios

### Scenario 1: Counselor Login and Dashboard
1. Login as counselor
2. Verify dashboard loads with correct statistics
3. Check appointment lists
4. Verify notification count

### Scenario 2: Schedule Management
1. Create schedule for Monday 9AM-5PM
2. Set max appointments to 5
3. Toggle availability off and on
4. Edit schedule time
5. Delete schedule

### Scenario 3: Appointment Management
1. View all assigned appointments
2. Filter by status (pending, confirmed, completed)
3. Update appointment status
4. Add counselor notes
5. Cancel appointment

### Scenario 4: Notification System
1. Create test notification
2. Mark as read
3. Delete notification
4. Check unread count

## Troubleshooting

### Common Issues:

**1. "No counselor found" error:**
```php
// Check if counselor exists
\App\Models\User::where('role', 'counselor')->count();
```

**2. "Schedule not found" error:**
```php
// Check counselor schedules
$counselor = \App\Models\User::where('role', 'counselor')->first();
$counselor->schedules;
```

**3. "Appointment not found" error:**
```php
// Check appointments
\App\Models\Appointment::where('counselor_id', $counselor->id)->get();
```

### Debugging Commands:

```php
// Check all users and roles
\App\Models\User::all(['id', 'email', 'role']);

// Check all schedules
\App\Models\Schedule::all();

// Check all appointments
\App\Models\Appointment::with(['user', 'counselor'])->get();

// Check notifications
\App\Models\Notification::all();
```

## Cleanup Commands

After testing, clean up test data:

```php
// Remove test appointments
\App\Models\Appointment::where('user_id', $studentId)->delete();

// Remove test schedules
\App\Models\Schedule::where('counselor_id', $counselorId)->where('day_of_week', 'saturday')->delete();

// Remove test notifications
\App\Models\Notification::where('title', 'Test Notification')->delete();

// Remove test users
\App\Models\User::where('email', 'teststudent@example.com')->delete();
```

## Performance Testing

### Test with Large Data Sets:
```php
// Create 100 test appointments
for ($i = 1; $i <= 100; $i++) {
    \App\Models\Appointment::create([
        'user_id' => $student->id,
        'counselor_id' => $counselor->id,
        'appointment_date' => now()->addDays($i),
        'start_time' => '10:00',
        'end_time' => '11:00',
        'type' => 'regular',
        'reason' => 'Test appointment ' . $i,
        'status' => 'pending',
    ]);
}
```

## Security Testing

### Test Role-Based Access:
1. Try accessing counselor routes as student
2. Try accessing counselor routes as faculty
3. Verify middleware protection works
4. Test appointment ownership validation

### Test Data Validation:
1. Submit invalid appointment data
2. Submit invalid schedule data
3. Test SQL injection prevention
4. Test XSS prevention

## Conclusion

This testing guide provides comprehensive coverage of all counselor features. Use the methods that work best for your testing needs:

- **Tinker**: For comprehensive automated testing
- **DD**: For quick debugging and data inspection
- **Manual Testing**: For user experience validation
- **Browser Testing**: For end-to-end functionality verification

Remember to clean up test data after testing to maintain a clean database state. 