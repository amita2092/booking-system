<?php

namespace App\Listeners;

use App\Events\AppointmentRescheduled;
use App\Models\Notification as NotificationLog;
use App\Notifications\AppointmentStatusNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateRescheduleNotification implements ShouldQueue
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
    public function handle(AppointmentRescheduled $event): void
    {
        $appointment = $event->appointment->fresh();
        $patient = $appointment->patient;

        $notification = NotificationLog::create([
            'appointment_id' => $appointment->id,
            'user_id' => $patient->id,
            'type' => 'appointment_rescheduled_email',
            'payload' => [
                'reference_number' => $appointment->reference_number,
                'appointment_status' => 'rescheduled',
                'email' => $patient->email,
                'subject' => 'Appointment Rescheduled',
                'slot_id' => $appointment->slot_id,
            ],
            'status' => 'processing',
            'retry_count' => max(0, $this->attempts() - 1),
        ]);

        try {
            $patient->notify(
                new AppointmentStatusNotification($appointment, 'rescheduled')
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
