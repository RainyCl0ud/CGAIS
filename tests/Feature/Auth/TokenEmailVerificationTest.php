<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TokenEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified_with_token(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();
        Notification::fake();

        // Simulate registration by sending the custom notification
        $user->notify(new CustomVerifyEmail());

        // Get the token that was generated
        $token = $user->fresh()->email_verification_token;
        $this->assertNotNull($token);

        // Verify the email using the token
        $response = $this->actingAs($user)->get("/verify-email?token={$token}");

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->email_verification_token); // Token should be cleared
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_verification_fails_with_invalid_token(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email?token=invalid-token');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/verify-email');
        $response->assertSessionHasErrors('token');
    }

    public function test_already_verified_user_cannot_verify_again(): void
    {
        $user = User::factory()->create(); // Creates verified user by default

        $response = $this->actingAs($user)->get('/verify-email?token=some-token');

        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_resend_verification_email_works(): void
    {
        $user = User::factory()->unverified()->create();

        Notification::fake();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertSentTo($user, CustomVerifyEmail::class);
        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
    }
}
