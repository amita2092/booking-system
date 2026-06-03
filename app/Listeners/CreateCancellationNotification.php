<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Models\Notification as NotificationLog;
use App\Notifications\AppointmentStatusNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateCancellationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentCancelled $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;

        $notification = NotificationLog::create([
            'appointment_id' => $appointment->id,
            'user_id' => $patient->id,
            'type' => 'appointment_cancelled_email',
            'payload' => [
                'reference_number' => $appointment->reference_number,
                'appointment_status' => 'cancelled',
                'email' => $patient->email,
                'subject' => 'Appointment Cancelled',
            ],
            'status' => 'processing',
            'retry_count' => max(0, $this->attempts() - 1),
        ]);

        try {
            $patient->notify(
                new AppointmentStatusNotification($appointment, 'cancelled')
            );

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Exception $exception) {
            $notification->update([
                'status' => 'failed',
                'retry_count' => $this->attempts(),
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
