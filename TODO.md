# TODO: Add Separate Feedback Form Document Code Management

## Step 1: Modify DocumentCode Model
- [x] Add 'type' field to fillable array (enum: 'pds', 'feedback_form')

## Step 2: Create Migration
- [x] Create new migration to add 'type' column to document_codes table
- [x] Set existing record type to 'pds'
- [x] Insert new record for 'feedback_form' with 'FM-USTP-GCS-01'

## Step 3: Update DocumentCodeSeeder
- [x] Seed both PDS and Feedback Form document codes

## Step 4: Update DocumentCodeController
- [x] Modify index() to fetch both PDS and Feedback Form codes
- [x] Update update() to handle type-specific updates (add type parameter)

## Step 5: Update document-codes/index.blade.php
- [x] Display two separate forms: one for PDS, one for Feedback Form

## Step 6: Update All Usages
- [x] PersonalDataSheetController: Change DocumentCode::first() to where('type', 'pds')->first()
- [x] FeedbackFormController: Change DocumentCode::first() to where('type', 'feedback_form')->first()
- [x] StudentManagementController: Update accordingly (likely PDS)
- [x] resources/views/pdfs/feedback.blade.php: Ensure it uses feedback_form type

## Step 7: Run Migrations and Seeders
- [x] Execute migrations
- [x] Run seeders

## Step 8: Test
- [x] Verify PDS displays use 'pds' type
- [x] Verify Feedback Form displays use 'feedback_form' type
- [x] Ensure no breaking changes to existing functionality
