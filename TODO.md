# Fix Booking Logic for Counselor Availability

## Issue Summary
The booking logic for students, faculty, and staff does not properly reflect the counselor's updated availability. Even when counselors mark dates as unavailable or have available hours, the booking view shows "No time available on this date."

## Root Cause
- The `getAvailableSlots` methods in `StudentAppointmentController` and `AppointmentController` only check the counselor's weekly schedule (`Schedule` model) but do not check the `CounselorUnavailableDate` model for specific unavailable dates.
- The `store` methods in both controllers validate availability based on schedule but ignore unavailable dates.
- No caching is involved; the issue is missing logic to query `CounselorUnavailableDate`.
- Additionally, there was a mismatch between integer day_of_week (0-6) used in code and string enum ('monday', etc.) in database.

## Tasks
- [x] Update `StudentAppointmentController::getAvailableSlots` to check `CounselorUnavailableDate` for the specific date and counselor.
- [x] Update `AppointmentController::getAvailableSlots` to check `CounselorUnavailableDate` for the specific date and counselor.
- [x] Update `StudentAppointmentController::store` to validate that the appointment date is not marked as unavailable for the counselor.
- [x] Update `AppointmentController::store` to validate that the appointment date is not marked as unavailable for the counselor.
- [x] Fix day_of_week mapping from integer to string in all relevant methods.
- [x] Update `getAvailableDates` methods in both controllers to exclude dates where all counselors are unavailable.
- [ ] Test the changes to ensure available slots are shown correctly and unavailable dates prevent booking.
- [ ] Verify that changes work for students, faculty, and staff users.

## Files to Edit
- `app/Http/Controllers/StudentAppointmentController.php`
- `app/Http/Controllers/AppointmentController.php`

## Followup Steps
- After editing, run tests to ensure no regressions.
- Clear any application caches if needed (though not expected).
- Deploy and monitor for correct behavior.
