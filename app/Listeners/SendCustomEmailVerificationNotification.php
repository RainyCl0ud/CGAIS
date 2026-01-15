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
        $event->user->notify(new CustomVerifyEmail());
    }
}
