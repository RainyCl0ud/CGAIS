<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\View;

class PdsPdfViewTest extends TestCase
{
    public function test_pds_pdf_view_renders_with_sample_data()
    {
        $student = (object) [
            'getFullNameAttribute' => 'Sample Student',
            'student_id' => 'S12345',
            'course_category' => 'BSCS',
            'year_level' => '3'
        ];

        $pds = (object) [
            'course' => 'BSCS',
            'major' => 'Computer Science',
            'year_level' => '3',
            'first_name' => 'Sample',
            'middle_name' => '',
            'last_name' => 'Student',
            'birth_date' => null,
            'contact_number' => null,
            'email' => null,
            'permanent_address' => null,
            'present_address' => null,
        ];

        $logos = ['logo' => null];
        $photoData = null;

        $view = view('pdfs.pds', compact('student','pds','logos','photoData'))->render();

        $this->assertStringContainsString("STUDENT'S PERSONAL DATA SHEET", strtoupper($view));
        $this->assertStringContainsString('Sample Student', $view);
    }
}
