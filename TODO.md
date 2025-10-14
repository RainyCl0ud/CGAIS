# TODO: Implement Interactive Calendar for Counselor Schedule Management

## Steps:

1. **[x]** Verify and add routes in `routes/web.php` for `toggleUnavailableDate` (POST) and `getUnavailableDates` (GET) if missing.
   - Ensure named routes: `schedules.toggleUnavailableDate` and `schedules.getUnavailableDates`.
   - Middleware: auth, counselor-only if needed.
   - Status: Routes for toggle exist; get not needed (server-side load).

2. **[x]** Edit `resources/views/schedules/index.blade.php`:
   - Add `isWeekend` property to calendarDays objects (true for Sat/Sun).
   - Update button `:class` to style weekends gray/disabled, unavailable weekdays red.
   - In `selectDate()`: Add check to return early if weekend.
   - Refine `updateTableStatus()`: Match exact date in table row's text (e.g., "Next: Oct 14, 2025") to update only matching row's status.
   - Status: Edits applied successfully; weekends grayed/disabled, unavailable weekdays red, toggles skip weekends, table updates exact matches.

3. **[x]** Verify routes: Run `php artisan route:list | grep schedules` to confirm.
   - Status: Confirmed; POST schedules/toggle-unavailable-date named schedules.toggleUnavailableDate exists under counselor_only middleware.

4. **[ ]** Test functionality:
   - Start server if needed (`php artisan serve`).
   - Use browser to navigate to /schedules, toggle weekday (should go red, update DB), toggle weekend (no action), check table update for matching dates.

5. **[ ]** Discuss UI/UX with user after function implementation.

Progress will be updated here after each step.
