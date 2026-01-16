<?php

namespace App\Listeners;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Registered;

class SendCustomEmailVerificationNotification
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Only send verification email if user hasn't been sent one yet
        if (!$event->user->email_verification_token) {
            $event->user->notify(new CustomVerifyEmail());
        }
    }
}
