# TODO for Authorized IDs Simplification

## Overview
Simplify the Authorized IDs feature for counselors: Enforce single ID creation at a time, remove notes functionality, simplify UI/UX (e.g., single input instead of large textarea), remove statistic cards and status filter from index view, handle errors/success messages. Core logic remains functional.

## Steps

- [x] **Update resources/views/authorized-ids/index.blade.php**:
  - Remove the entire statistics cards section (grid with 5 cards: Total, Available, Used, Students, Faculty).
  - In the filters form: Remove the status select dropdown (including label and grid column). Adjust grid classes to grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 for Search, Type, and Filter button.
  - Ensure table and other elements remain intact.

- [x] **Update resources/views/authorized-ids/create.blade.php**:
  - Replace the id_numbers textarea (rows=10) with a single text input for id_number (required, min length 3, placeholder for one ID).
  - Remove the entire notes section (label, textarea, error handling).
  - Update labels: "ID Number" instead of "ID Numbers (one per line)".
  - Change submit button text to "Add ID" (singular).
  - Retain type select, form structure, and error handling.

- [x] **Update app/Http/Controllers/AuthorizedIdController.php**:
  - In `index()`: Remove the $stats array computation and compact('stats'). Remove the status filter logic (if $request->filled('status') block).
  - In `store()`: 
    - Update validation: Change 'id_numbers' to 'id_number' (required|string|min:3|max:50), remove 'notes'.
    - Adjust logic: Use $request->id_number directly (trim, check length), single existing ID check with where('id_number', ...), single create() call without notes.
    - Update success/error messages for single ID (e.g., "Successfully created authorized ID.", "ID already exists: {id}").
  - In `update()`: Remove 'notes' from validation and update array.
  - Other methods unchanged.

- [x] **Update resources/views/authorized-ids/edit.blade.php**:
  - Remove the notes section (label, textarea, error handling).
  - Retain id_number input, type select, form structure.

- [x] **Followup - Cache Clearing**:
  - Run `php artisan view:clear` and `php artisan route:clear` to ensure changes take effect.

- [x] **Testing**:
  - Verify create form: Submit single valid ID (success redirect to index with message), invalid/duplicate (error display), no notes field.
  - Verify index: No stats cards, filters only Search/Type/Filter (status gone), table shows status but no filter applied.
  - Verify edit: No notes field, update id_number/type works.
  - Check success/error messages display properly (e.g., via session flashes in layout).

## Notes
- No DB changes (notes field can remain but unused).
- Dependent: Views rely on updated controller data; test after all edits.
- Update this TODO.md after completing each step by marking [x].
