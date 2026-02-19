# PDS Empty Fields Fix - TODO List

## Task
Remove all auto-fill logic from PDS form so all textboxes are empty and users must fill everything including first name.

## Fields to Update in resources/views/pds/show.blade.php

- [ ] 1. Year Level - Remove auto-fill from Auth::user()->year_level
- [ ] 2. First Name - Remove auto-fill from Auth::user()->first_name
- [ ] 3. Middle Name - Remove auto-fill from Auth::user()->middle_name
- [ ] 4. Last Name - Remove auto-fill from Auth::user()->last_name
- [ ] 5. Contact Number - Remove auto-fill from Auth::user()->phone_number
- [ ] 6. Email - Remove auto-fill from Auth::user()->email
- [ ] 7. Signature - Remove auto-fill logic from Auth::user()

## Notes
- Course/Track field is already updated (no auto-fill)
- After changes, all fields will be empty by default
- Printing functionality should still work correctly with user input
