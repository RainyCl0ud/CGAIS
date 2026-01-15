# Fix Appointment Type Display Bug

## Issue
When faculty users book an appointment and select "Referral" as the consultation type, it is stored correctly as 'urgent' in the database, but displayed as "Urgent" instead of "Referral" in the "My Appointments" table and other views.

## Root Cause
Views are using `{{ ucfirst($appointment->type) }}` which displays "Urgent" for type 'urgent', but the Appointment model has a `getTypeLabel()` method that correctly returns "Referral" for faculty users.

## Solution
Replace `{{ ucfirst($appointment->type) }}` with `{{ $appointment->getTypeLabel() }}` in all relevant views.

## Files to Update
- [ ] resources/views/student/appointments/index.blade.php
- [ ] resources/views/student/appointments/show.blade.php
- [ ] resources/views/student/appointments/session-history.blade.php
- [ ] resources/views/livewire/appointment-manager.blade.php
- [ ] resources/views/dashboard/student.blade.php
- [ ] resources/views/appointments/index.blade.php
- [ ] resources/views/appointments/session-history.blade.php
- [ ] resources/views/appointments/show.blade.php
- [ ] resources/views/appointments/pending.blade.php
- [ ] resources/views/appointments/today.blade.php
- [ ] resources/views/user-management/show.blade.php
- [ ] resources/views/student-management/show.blade.php

## Testing
- Book appointment as faculty user selecting "Referral"
- Check that it displays as "Referral" in My Appointments and other views
