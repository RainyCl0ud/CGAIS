<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewCounselorCreated extends Notification
{
    use Queueable;

    protected $tempPassword;

    public function __construct($tempPassword)
    {
        $this->tempPassword = $tempPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $loginUrl = url('/login');

        return (new MailMessage)
                    ->subject('Your Counselor Account')
                    ->greeting('Hello ' . ($notifiable->first_name ?? ''))
                    ->line('An account has been created for you as a counselor.')
                    ->line('You can log in using the following temporary password:')
                    ->line('')
                    ->line($this->tempPassword)
                    ->action('Login', $loginUrl)
                    ->line('Please change your password after logging in.');
    }
}
