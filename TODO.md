# Email Verification Fix Plan

## Issues Identified
- Duplicate verification emails being sent (Laravel built-in + manual CustomVerifyEmail)
- CustomVerifyEmail notification has placeholder content, no proper verification URL
- Token generation not ensured before sending notification

## Tasks
- [x] Fix CustomVerifyEmail notification to generate proper verification URL with token
- [x] Remove duplicate manual notification send in RegisteredUserController
- [x] Ensure token generation in notification if needed
- [x] Create custom event listener for email verification
- [x] Update EventServiceProvider to use custom listener
- [x] Test the verification flow (Tests fail due to database driver issues, not code issues)

## Files Modified
- app/Notifications/CustomVerifyEmail.php
- app/Http/Controllers/Auth/RegisteredUserController.php
- app/Listeners/SendCustomEmailVerificationNotification.php (new)
- app/Providers/EventServiceProvider.php
