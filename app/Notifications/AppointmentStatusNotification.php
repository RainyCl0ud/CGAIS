<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentStatusNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $action;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param Appointment $appointment
     * @param string $action - 'approved', 'rescheduled', 'cancelled'
     * @param string|null $reason
     */
    public function __construct(Appointment $appointment, string $action, ?string $reason = null)
    {
        $this->appointment = $appointment;
        $this->action = $action;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appointmentDate = $this->appointment->appointment_date->format('l, F j, Y');
        $appointmentTime = $this->appointment->start_time->format('g:i A') . ' - ' . $this->appointment->end_time->format('g:i A');
        
        $subject = $this->getSubject();
        $greeting = 'Hello ' . $notifiable->full_name . '!';
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);

        switch ($this->action) {
            case 'approved':
                $message->line('Great news! Your appointment has been approved.')
                    ->line('')
                    ->line("**Appointment Details:**")
                    ->line("📅 **Date:** {$appointmentDate}")
                    ->line("🕐 **Time:** {$appointmentTime}")
                    ->line("👤 **Counselor:** " . ($this->appointment->counselor->full_name ?? 'Your Counselor'))
                    ->line("🏥 **Type:** " . $this->appointment->getCounselingCategoryLabel())
                    ->line('')
                    ->line('Please make sure to be on time for your appointment.');
                break;

            case 'rescheduled':
                $message->line('Your appointment has been rescheduled.')
                    ->line('')
                    ->line("**New Appointment Details:**")
                    ->line("📅 **Date:** {$appointmentDate}")
                    ->line("🕐 **Time:** {$appointmentTime}")
                    ->line("👤 **Counselor:** " . ($this->appointment->counselor->full_name ?? 'Your Counselor'))
                    ->line("🏥 **Type:** " . $this->appointment->getCounselingCategoryLabel());
                
                if ($this->reason) {
                    $message->line('')
                        ->line("📝 **Reason for rescheduling:** {$this->reason}");
                }
                
                $message->line('')
                    ->line('Please take note of the new schedule.');
                break;

            case 'cancelled':
                $message->line('We regret to inform you that your appointment has been cancelled.')
                    ->line('')
                    ->line("**Cancelled Appointment Details:**")
                    ->line("📅 **Date:** {$appointmentDate}")
                    ->line("🕐 **Time:** {$appointmentTime}")
                    ->line("👤 **Counselor:** " . ($this->appointment->counselor->full_name ?? 'Your Counselor'));

                if ($this->reason) {
                    $message->line('')
                        ->line("📝 **Reason for cancellation:** {$this->reason}");
                }

                $message->line('')
                    ->line('If you would like to schedule a new appointment, please visit our system.');
                break;
        }

        // Add button that links to login page (will redirect to dashboard if already logged in)
        $message->action('Go to Dashboard', url('/login'))
            ->line('')
            ->line('If you are already logged in, clicking the button above will take you directly to your dashboard.')
            ->line('')
            ->line('Thank you for using our counseling services!');

        return $message;
    }

    /**
     * Get the subject based on action.
     */
    protected function getSubject(): string
    {
        switch ($this->action) {
            case 'approved':
                return 'Your Appointment Has Been Approved';
            case 'rescheduled':
                return 'Your Appointment Has Been Rescheduled';
            case 'cancelled':
                return 'Your Appointment Has Been Cancelled';
            case 'rejected':
                return 'Your Appointment Has Been Rejected';
            default:
                return 'Appointment Status Update';
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'action' => $this->action,
            'appointment_date' => $this->appointment->appointment_date->toDateString(),
            'appointment_time' => $this->appointment->start_time->format('H:i'),
            'reason' => $this->reason,
        ];
    }
}
