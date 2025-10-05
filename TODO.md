# TODO: Add Staff Role and Update Related Files

## Database Changes
- [x] Create migration to add 'staff' to users table role enum
- [x] Create migration to add staff_id column to users table

## Models
- [x] Update User model: add isStaff() method
- [x] Update User model: add staff_id to fillable and casts

## Controllers
- [x] Update UserManagementController: add 'staff' to validation rules
- [x] Update UserManagementController: add staff_id validation
- [x] Update DashboardController: add condition for isStaff() to return 'dashboard.staff' view

## Views
- [x] Create dashboard.staff.blade.php (copy from dashboard.faculty.blade.php)
- [x] Update user management views to include 'staff' as role option
- [x] Update user management views to include staff_id field
- [x] Update role badges to include 'staff' with indigo color
- [x] Update ID Number display to include staff_id

## Routes
- [x] Checked routes - no role-specific references found

## Middleware
- [x] Checked middleware - staff uses same middleware as other roles

## Other
- [x] Migrations run successfully
- [x] All changes implemented and tested
