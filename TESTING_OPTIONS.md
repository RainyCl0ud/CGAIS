# Counselor Testing Options Guide

## 🎯 Quick Start - Choose Your Method

Since Tinker can be problematic on Windows terminals, here are **5 better alternatives**:

## Option 1: Simple PHP Script (Recommended for Windows)
**Best for:** Quick testing, Windows compatibility, no terminal issues

```bash
php test_counselor_simple.php
```

**Pros:**
- ✅ Works on all Windows terminals
- ✅ No copy/paste issues
- ✅ Instant results
- ✅ Easy to modify

**What it tests:**
- Counselor authentication
- Schedule management
- Appointment creation
- Dashboard statistics
- Notifications
- Role-based access

## Option 2: Laravel Artisan Command
**Best for:** Professional testing, CI/CD integration

```bash
# Basic test
php artisan test:counselor

# Test with data creation
php artisan test:counselor --create-data

# Test with cleanup
php artisan test:counselor --cleanup

# Full test cycle
php artisan test:counselor --create-data --cleanup
```

**Pros:**
- ✅ Professional output
- ✅ Command-line options
- ✅ Can be automated
- ✅ Laravel standard

## Option 3: Browser Testing (Manual)
**Best for:** User experience testing, visual verification

**Login Credentials:**
- Email: `counselor@gmail.com`
- Password: `admin`

**Test URLs:**
- Dashboard: `/dashboard`
- Appointments: `/appointments`
- Schedules: `/schedules`
- Notifications: `/notifications`

## Option 4: Controller Method Testing
**Best for:** Debugging specific features

Add this to any controller method:

```php
// Quick DD test
$counselor = \App\Models\User::where('role', 'counselor')->first();
dd([
    'counselor' => $counselor,
    'schedules' => $counselor->schedules,
    'appointments' => $counselor->counselorAppointments,
    'notifications' => $counselor->notifications,
]);
```

## Option 5: Database Query Testing
**Best for:** Data verification, troubleshooting

```bash
# Check all users
php artisan tinker --execute="echo \App\Models\User::all(['id', 'email', 'role']);"

# Check counselor data
php artisan tinker --execute="echo \App\Models\User::where('role', 'counselor')->first();"

# Check appointments
php artisan tinker --execute="echo \App\Models\Appointment::all();"
```

## 🧪 Testing Scenarios

### Scenario 1: Fresh Installation Test
```bash
# 1. Seed the database
php artisan db:seed

# 2. Run comprehensive test
php test_counselor_simple.php

# 3. Verify results
php artisan test:counselor
```

### Scenario 2: Feature Testing
```bash
# Test with data creation
php artisan test:counselor --create-data

# Login and test manually
# Email: counselor@example.com
# Password: counselorpass

# Clean up after testing
php artisan test:counselor --cleanup
```

### Scenario 3: Performance Testing
```bash
# Create large dataset
php artisan test:counselor --create-data

# Test dashboard performance
# Navigate to /dashboard

# Clean up
php artisan test:counselor --cleanup
```

## 🔧 Troubleshooting

### Issue: "No counselor found"
**Solution:**
```bash
php artisan db:seed
```

### Issue: "Database connection failed"
**Solution:**
```bash
# Check your .env file
php artisan config:clear
php artisan cache:clear
```

### Issue: "Permission denied"
**Solution:**
```bash
# On Windows, run PowerShell as Administrator
# Or use the simple PHP script instead
php test_counselor_simple.php
```

### Issue: "Tinker not working"
**Solution:**
- Use the simple PHP script: `php test_counselor_simple.php`
- Use the Artisan command: `php artisan test:counselor`
- Use browser testing with the provided credentials

## 📊 Test Results Interpretation

### ✅ All Tests Pass
- Counselor authentication working
- Schedule management functional
- Appointment system operational
- Dashboard statistics accurate
- Notifications working
- Role-based access secure

### ❌ Some Tests Fail
- Check database connection
- Verify seeded data exists
- Review error messages
- Run `php artisan db:seed` if needed

## 🚀 Advanced Testing

### Load Testing
```bash
# Create 100 test appointments
php artisan test:counselor --create-data
# Then manually test dashboard performance
```

### Security Testing
```bash
# Test role-based access
# Try accessing counselor routes as student
# Verify middleware protection
```

### Data Validation Testing
```bash
# Submit invalid data through browser
# Test form validation
# Verify error handling
```

## 📝 Custom Testing

### Modify the Simple Script
Edit `test_counselor_simple.php` to add your own tests:

```php
// Add custom test
echo "\n8. Custom Test...\n";
// Your test code here
```

### Create New Artisan Command
```bash
php artisan make:command TestCustomFeature
```

### Add to Existing Command
Edit `app/Console/Commands/TestCounselorFeatures.php` to add new test methods.

## 🎯 Recommended Workflow

1. **Start with Simple Script:**
   ```bash
   php test_counselor_simple.php
   ```

2. **Use Artisan for Detailed Testing:**
   ```bash
   php artisan test:counselor --create-data
   ```

3. **Test Manually in Browser:**
   - Login with provided credentials
   - Test each feature visually
   - Verify user experience

4. **Clean Up:**
   ```bash
   php artisan test:counselor --cleanup
   ```

## 📋 Quick Reference

| Method | Command | Best For |
|--------|---------|----------|
| Simple Script | `php test_counselor_simple.php` | Windows, quick tests |
| Artisan Command | `php artisan test:counselor` | Professional testing |
| Browser Testing | Manual login | User experience |
| DD Debugging | Add to controllers | Specific debugging |
| Database Queries | `php artisan tinker --execute="..."` | Data verification |

## 🔑 Login Credentials Summary

### Existing Test Data
The system comes with pre-seeded test data:

**Counselor Account:**
- Email: `counselor@gmail.com`
- Password: `admin`

**Student Account:**
- Email: `student@gmail.com`
- Password: `admin`

**Assistant Account:**
- Email: `assistant@gmail.com`
- Password: `admin`

**Faculty Account:**
- Email: `faculty@gmail.com`
- Password: `admin`
- Name: Jasmin Torres Caipang

## ✅ Success Indicators

When all tests pass, you should see:
- ✅ Counselor authentication: PASS
- ✅ Schedule management: PASS
- ✅ Appointment management: PASS
- ✅ Dashboard statistics: PASS
- ✅ Notifications: PASS
- ✅ Role-based access: PASS

**All counselor features are working correctly!** 