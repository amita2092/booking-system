<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Appointment $appointment,
        public string $status
    ) {}

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
    public function toMail($notifiable): MailMessage
    {
        return match ($this->status) {

            'booked' => (new MailMessage)
                ->subject('Appointment Booked Successfully')
                ->greeting('Hello ' . $notifiable->name)
                ->line('Your appointment has been booked.')
                ->line('Reference: ' . $this->appointment->reference_number)
                ->line('Thank you for choosing us.'),

            'cancelled' => (new MailMessage)
                ->subject('Appointment Cancelled')
                ->greeting('Hello ' . $notifiable->name)
                ->line('Your appointment has been cancelled.')
                ->line('Reference: ' . $this->appointment->reference_number),

            'rescheduled' => (new MailMessage)
                ->subject('Appointment Rescheduled')
                ->greeting('Hello ' . $notifiable->name)
                ->line('Your appointment has been rescheduled.')
                ->line('New Slot ID: ' . $this->appointment->slot_id)
                ->line('Reference: ' . $this->appointment->reference_number),

            default => (new MailMessage)
                ->subject('Appointment Update')
                ->line('Your appointment status has changed.'),
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
