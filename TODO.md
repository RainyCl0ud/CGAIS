# TODO: Modify Awards Section in PDS

## Pending Tasks
- [x] Create a new migration to alter the 'awards' column from text to JSON to store structured data.
- [x] Update the PersonalDataSheet model to cast 'awards' as an array.
- [x] Update the PersonalDataSheetController validation to handle 'awards' as an array of objects (each with award, school, year).
- [x] Modify the awards section in show.blade.php to render 4 sets of 3 input fields each (award name, school/organization, year).

## Followup Steps
- [x] Run the new migration to update the database schema.
- [x] Add signature and signature_date fields to database and form
- [x] Test the form submission and data storage to ensure awards are saved as structured data.
