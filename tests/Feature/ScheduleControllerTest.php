<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CounselorUnavailableDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $counselor;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a counselor user
        $this->counselor = User::factory()->create([
            'role' => 'counselor',
        ]);
    }

    public function test_get_unavailable_dates_requires_authentication()
    {
        $response = $this->getJson(route('schedules.getUnavailableDates'));
        $response->assertStatus(403);
    }

    public function test_get_unavailable_dates_returns_dates_for_counselor()
    {
        CounselorUnavailableDate::factory()->create([
            'counselor_id' => $this->counselor->id,
            'date' => '2025-10-10',
        ]);

        $response = $this->actingAs($this->counselor)->getJson(route('schedules.getUnavailableDates'));
        $response->assertStatus(200)
            ->assertJsonFragment(['unavailableDates' => ['2025-10-10']]);
    }

    public function test_toggle_unavailable_date_requires_authentication()
    {
        $response = $this->postJson(route('schedules.toggleUnavailableDate'), ['date' => '2025-10-10']);
        $response->assertStatus(403);
    }

    public function test_toggle_unavailable_date_marks_and_unmarks_date()
    {
        // Mark date as unavailable
        $response = $this->actingAs($this->counselor)->postJson(route('schedules.toggleUnavailableDate'), ['date' => '2025-10-10']);
        $response->assertStatus(200)
            ->assertJson(['status' => 'unavailable', 'date' => '2025-10-10']);

        $this->assertDatabaseHas('counselor_unavailable_dates', [
            'counselor_id' => $this->counselor->id,
            'date' => '2025-10-10',
            'is_unavailable' => true,
        ]);

        // Toggle again to mark as available (remove)
        $response = $this->actingAs($this->counselor)->postJson(route('schedules.toggleUnavailableDate'), ['date' => '2025-10-10']);
        $response->assertStatus(200)
            ->assertJson(['status' => 'available', 'date' => '2025-10-10']);

        $this->assertDatabaseMissing('counselor_unavailable_dates', [
            'counselor_id' => $this->counselor->id,
            'date' => '2025-10-10',
        ]);
    }

    public function test_toggle_unavailable_date_requires_valid_date()
    {
        $response = $this->actingAs($this->counselor)->postJson(route('schedules.toggleUnavailableDate'), ['date' => 'invalid-date']);
        $response->assertStatus(422);
    }
}
