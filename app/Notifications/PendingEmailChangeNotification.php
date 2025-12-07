<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PendingEmailChangeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $newEmail,
        public string $verificationUrl
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your New Email Address')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('You have requested to change your email address from "' . $notifiable->email . '" to "' . $this->newEmail . '".')
            ->line('Please click the button below to verify your new email address:')
            ->action('Verify New Email', $this->verificationUrl)
            ->line('If you did not request this email change, please ignore this message.')
            ->line('This verification link will expire in 24 hours.');
    }
}