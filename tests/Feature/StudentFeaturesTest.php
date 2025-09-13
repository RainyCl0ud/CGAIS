<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PersonalDataSheet;
use App\Models\FeedbackForm;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_pds_page()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/pds');

        $response->assertStatus(200);
        $response->assertViewIs('pds.show');
    }

    public function test_student_can_edit_pds()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/pds/edit');

        $response->assertStatus(200);
        $response->assertViewIs('pds.edit');
    }

    public function test_student_can_update_pds()
    {
        $student = User::factory()->create(['role' => 'student']);

        $pdsData = [
            'birth_date' => '1995-01-15',
            'birth_place' => 'Manila',
            'sex' => 'male',
            'civil_status' => 'single',
            'citizenship' => 'Filipino',
            'mobile_number' => '09123456789',
            'permanent_address' => '123 Main St, Manila',
            'father_name' => 'John Doe',
            'mother_name' => 'Jane Doe',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09123456789',
        ];

        $response = $this->actingAs($student)->patch('/pds', $pdsData);

        $response->assertRedirect('/pds');
        $this->assertDatabaseHas('personal_data_sheets', [
            'user_id' => $student->id,
            'birth_place' => 'Manila',
            'sex' => 'male',
        ]);
    }

    public function test_student_can_auto_save_pds()
    {
        $student = User::factory()->create(['role' => 'student']);

        $pdsData = [
            'birth_date' => '1995-01-15',
            'birth_place' => 'Manila',
            'sex' => 'male',
        ];

        $response = $this->actingAs($student)->postJson('/pds/auto-save', $pdsData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('personal_data_sheets', [
            'user_id' => $student->id,
            'birth_place' => 'Manila',
        ]);
    }

    public function test_student_can_access_feedback_index()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/feedback');

        $response->assertStatus(200);
        $response->assertViewIs('feedback.index');
    }

    public function test_student_can_create_feedback()
    {
        $student = User::factory()->create(['role' => 'student']);
        $counselor = User::factory()->create(['role' => 'counselor']);

        $response = $this->actingAs($student)->get('/feedback/create');

        $response->assertStatus(200);
        $response->assertViewIs('feedback.create');
    }

    public function test_student_can_store_feedback()
    {
        $student = User::factory()->create(['role' => 'student']);
        $counselor = User::factory()->create(['role' => 'counselor']);

        $feedbackData = [
            'counselor_id' => $counselor->id,
            'counselor_rating' => 5,
            'service_rating' => 4,
            'facility_rating' => 4,
            'overall_satisfaction' => 5,
            'counselor_feedback' => 'Great counselor!',
            'service_feedback' => 'Excellent service',
            'would_recommend' => true,
        ];

        $response = $this->actingAs($student)->post('/feedback', $feedbackData);

        $response->assertRedirect('/feedback');
        $this->assertDatabaseHas('feedback_forms', [
            'user_id' => $student->id,
            'counselor_id' => $counselor->id,
            'counselor_rating' => 5,
        ]);
    }

    public function test_student_can_book_appointment_with_counseling_category()
    {
        $student = User::factory()->create(['role' => 'student']);
        $counselor = User::factory()->create(['role' => 'counselor']);

        $appointmentData = [
            'counselor_id' => $counselor->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'type' => 'regular',
            'counseling_category' => 'counseling_services',
            'reason' => 'Need counseling support',
        ];

        $response = $this->actingAs($student)->post('/appointments', $appointmentData);

        $response->assertRedirect('/appointments');
        $this->assertDatabaseHas('appointments', [
            'user_id' => $student->id,
            'counselor_id' => $counselor->id,
            'counseling_category' => 'counseling_services',
        ]);
    }

    public function test_faculty_cannot_book_appointment_with_counseling_category()
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $counselor = User::factory()->create(['role' => 'counselor']);

        $appointmentData = [
            'counselor_id' => $counselor->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'type' => 'regular',
            'reason' => 'Need consultation',
        ];

        $response = $this->actingAs($faculty)->post('/appointments', $appointmentData);

        $response->assertRedirect('/appointments');
        $this->assertDatabaseHas('appointments', [
            'user_id' => $faculty->id,
            'counselor_id' => $counselor->id,
            'counseling_category' => 'consultation',
        ]);
    }

    public function test_pds_completion_percentage_calculation()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $pds = PersonalDataSheet::create([
            'user_id' => $student->id,
            'birth_date' => '1995-01-15',
            'birth_place' => 'Manila',
            'sex' => 'male',
            'mobile_number' => '09123456789',
            'permanent_address' => '123 Main St',
            'father_name' => 'John Doe',
            'mother_name' => 'Jane Doe',
            'emergency_contact_name' => 'Emergency',
            'emergency_contact_number' => '09123456789',
        ]);

        $this->assertEquals(100, $pds->getCompletionPercentage());
    }

    public function test_feedback_average_rating_calculation()
    {
        $feedback = FeedbackForm::create([
            'user_id' => 1,
            'counselor_id' => 2,
            'counselor_rating' => 5,
            'service_rating' => 4,
            'facility_rating' => 3,
            'overall_satisfaction' => 5,
        ]);

        $this->assertEquals(4.3, $feedback->getAverageRating());
    }

    public function test_student_middleware_restricts_access()
    {
        $faculty = User::factory()->create(['role' => 'faculty']);

        $response = $this->actingAs($faculty)->get('/pds');

        $response->assertStatus(403);
    }

    public function test_counseling_category_labels()
    {
        $appointment = new Appointment();
        $appointment->counseling_category = 'counseling_services';

        $this->assertEquals('Counseling Services', $appointment->getCounselingCategoryLabel());
    }

    public function test_counseling_category_badge_classes()
    {
        $appointment = new Appointment();
        $appointment->counseling_category = 'information_services';

        $this->assertEquals('bg-green-100 text-green-800', $appointment->getCounselingCategoryBadgeClass());
    }
} 