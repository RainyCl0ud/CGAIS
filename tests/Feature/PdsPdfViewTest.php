<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\View;

class PdsPdfViewTest extends TestCase
{
    public function test_pds_pdf_view_renders_with_sample_data()
    {
        $student = new class {
            public $student_id = 'S12345';
            public $course_category = 'BSCS';
            public $year_level = '3';
            public $first_name = 'Sample';
            public $last_name = 'Student';
            public $middle_name = '';
            public $phone_number = '09123456789';
            public $email = 'sample@student.edu';
            
            public function getFullNameAttribute() {
                return 'Sample Student';
            }
        };

        $pds = new class {
            public $course = 'BSCS';
            public $major = 'Computer Science';
            public $year_level = '3';
            public $first_name = 'Sample';
            public $middle_name = '';
            public $last_name = 'Student';
            public $birth_date = null;
            public $age = null;
            public $birth_place = null;
            public $sex = 'Male';
            public $civil_status = null;
            public $religion = null;
            public $contact_number = null;
            public $email = null;
            public $permanent_address = null;
            public $present_address = null;
            public $last_school = null;
            public $school_location = null;
            public $family_description = null;
            public $living_situation = null;
            public $living_condition = null;
            public $health_condition = null;
            public $signature = 'Sample Student';
            public $signature_date = null;
            public $signature_image = null;
        };

        $logos = ['logo' => null];
        $photoData = null;

        $view = view('pdfs.pds', compact('student','pds','logos','photoData'))->render();

        $this->assertStringContainsString("STUDENT'S PERSONAL DATA SHEET", strtoupper($view));
        $this->assertStringContainsString('Sample Student', $view);
    }
}
