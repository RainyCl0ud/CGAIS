# Manage Document Code Feature Implementation

## Tasks
- [x] Update DocumentCode migration to add fields: document_code_no, revision_no, effective_date, page_no
- [x] Update DocumentCode model with fillable fields
- [x] Create routes for document-codes (index, update)
- [x] Create DocumentCodeController with index and update methods
- [x] Create document-codes/index.blade.php view with form
- [x] Add "Manage Document Code" menu item to counselor sidebar
- [ ] Update PDS views to use dynamic DocumentCode values:
  - [ ] resources/views/pds/show.blade.php
  - [ ] resources/views/pds/edit.blade.php
  - [ ] resources/views/student-management/pds.blade.php
  - [ ] resources/views/pdfs/pds.blade.php
  - [ ] resources/views/pdfs/pds_html.blade.php
- [ ] Update DocumentCodeSeeder with default values

## Followup steps
- [ ] Run migration and seed database
- [ ] Test functionality
