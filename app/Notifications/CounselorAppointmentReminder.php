<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselorAppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $reminderType;

    /**
     * Create a new notification instance.
     */
    public function __construct($appointment, $reminderType = 'tomorrow')
    {
        $this->appointment = $appointment;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the notification's database representation.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $appointmentDate = $this->appointment->appointment_date->format('l, F j, Y');
        $appointmentTime = $this->appointment->start_time->format('g:i A') . ' - ' . $this->appointment->end_time->format('g:i A');
        $studentName = $this->appointment->user->full_name ?? 'Student';

        $title = $this->reminderType === 'tomorrow'
            ? 'Appointment Reminder - Tomorrow'
            : 'Appointment Reminder';

        $message = $this->reminderType === 'tomorrow'
            ? "You have an appointment with {$studentName} scheduled for tomorrow at {$appointmentTime}."
            : "You have an appointment with {$studentName} scheduled for today at {$appointmentTime}.";

        // Create in-app notification
        $notifiable->notifications()->create([
            'appointment_id' => $this->appointment->id,
            'title' => $title,
            'message' => $message,
            'type' => 'appointment_reminder',
            'is_read' => false,
            'read_at' => null,
        ]);

        return [
            'appointment_id' => $this->appointment->id,
            'appointment_date' => $this->appointment->appointment_date->toDateString(),
            'appointment_time' => $this->appointment->start_time->format('H:i'),
            'student_name' => $studentName,
            'reminder_type' => $this->reminderType,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appointmentDate = $this->appointment->appointment_date->format('l, F j, Y');
        $appointmentTime = $this->appointment->start_time->format('g:i A') . ' - ' . $this->appointment->end_time->format('g:i A');
        $studentName = $this->appointment->user->full_name ?? 'Student';
        $studentEmail = $this->appointment->user->email ?? 'N/A';
        
        $subject = $this->reminderType === 'tomorrow' 
            ? 'Appointment Reminder - Tomorrow' 
            : 'Appointment Reminder';

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line('This is a friendly reminder about an upcoming appointment.')
            ->line("**Appointment Details:**")
            ->line("📅 **Date:** {$appointmentDate}")
            ->line("🕐 **Time:** {$appointmentTime}")
            ->line("👤 **Student:** {$studentName}")
            ->line("📧 **Student Email:** {$studentEmail}")
            ->line("🏥 **Type:** {$this->appointment->getTypeLabel()}")
            ->line("📋 **Category:** {$this->appointment->getCounselingCategoryLabel()}");

        if ($this->appointment->reason) {
            $message->line("📝 **Reason:** {$this->appointment->reason}");
        }

        if ($this->reminderType === 'tomorrow') {
            $message->line('')
                ->line('⚠️ **Important:** Your appointment is scheduled for tomorrow. Please ensure you are available and prepared for the session.');
        }

        $message->line('')
            ->line('Thank you for using our counseling services!')
            ->action('View Appointment Details', url('/appointments/' . $this->appointment->id))
            ->line('If you have any questions, please don\'t hesitate to contact us.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $studentName = $this->appointment->user->full_name ?? 'Student';

        return [
            'appointment_id' => $this->appointment->id,
            'appointment_date' => $this->appointment->appointment_date->toDateString(),
            'appointment_time' => $this->appointment->start_time->format('H:i'),
            'student_name' => $studentName,
            'reminder_type' => $this->reminderType,
        ];
    }
}
