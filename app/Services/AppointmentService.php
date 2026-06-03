<?php

namespace App\Services;

use Exception;
use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneratesReferenceNumber;

class AppointmentService
{
    use GeneratesReferenceNumber;

    public function book(
        int $patientId,
        int $slotId
    ): Appointment {

        $appointment = DB::transaction(function () use (
            $patientId,
            $slotId
        ) {

            /*
             * Critical:
             * lock row to prevent concurrent booking
             */
            $slot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($slotId);

            if ($slot->status !== 'available') {
                throw new Exception(
                    'Slot is no longer available.'
                );
            }

            if (
                now()->greaterThan(
                    $slot->slot_start
                )
            ) {
                throw new Exception(
                    'Cannot book past slot.'
                );
            }

            $appointment = Appointment::create([
                'reference_number' =>
                    $this->generateReferenceNumber(),

                'doctor_id' =>
                    $slot->doctor_id,

                'patient_id' =>
                    $patientId,

                'slot_id' =>
                    $slot->id,

                'status' =>
                    'booked',

                'booked_at' =>
                    now(),
            ]);

            $slot->update([
                'status' => 'booked'
            ]);

            return $appointment;
        });

        AppointmentBooked::dispatch($appointment);

        return $appointment;
    }

    public function cancel(
            string $referenceNumber,
            string $reason
        ): Appointment {

            $appointment = DB::transaction(function () use (
                $referenceNumber,
                $reason
            ) {

                $appointment = Appointment::query()
                    ->lockForUpdate()
                    ->where(
                        'reference_number',
                        $referenceNumber
                    )
                    ->firstOrFail();

                if (
                    $appointment->status === 'cancelled'
                ) {
                    throw new \Exception(
                        'Appointment already cancelled.'
                    );
                }

                $appointment->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                ]);

                AppointmentSlot::where(
                    'id',
                    $appointment->slot_id
                )->update([
                    'status' => 'available'
                ]);

                return $appointment;
            });

            AppointmentCancelled::dispatch($appointment);

            return $appointment;
    }

    public function reschedule(
        string $referenceNumber,
        int $newSlotId
    ): Appointment {

        $appointment = DB::transaction(function () use (
            $referenceNumber,
            $newSlotId
        ) {

            $appointment = Appointment::query()
                ->lockForUpdate()
                ->where('reference_number', $referenceNumber)
                ->firstOrFail();

            if ($appointment->status === 'cancelled') {
                throw new \Exception(
                    'Cancelled appointment cannot be rescheduled.'
                );
            }

            $oldSlot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($appointment->slot_id);

            $newSlot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($newSlotId);

            // must belong to same doctor
            if ($oldSlot->doctor_id !== $newSlot->doctor_id) {
                throw new \Exception(
                    'You can only reschedule within the same doctor.'
                );
            }

            if ($newSlot->status !== 'available') {
                throw new \Exception(
                    'Selected slot is not available.'
                );
            }

            if (now()->greaterThan($newSlot->slot_start)) {
                throw new \Exception(
                    'Cannot reschedule to a past slot.'
                );
            }

            if ($oldSlot->id === $newSlot->id) {
                throw new \Exception(
                    'New slot must be different from current slot.'
                );
            }

            $oldSlot->update([
                'status' => 'available'
            ]);

            $newSlot->update([
                'status' => 'booked'
            ]);

            $appointment->update([
                'slot_id' => $newSlot->id,
                'status' => 'rescheduled'
            ]);

            return $appointment;
        });

        AppointmentRescheduled::dispatch($appointment);

        return $appointment;
    }

    public function doctorAppointments(
        int $doctorId,
        array $filters = []
    )
    {
        $query = Appointment::query()
            ->with([
                'patient',
                'slot',
            ])
            ->whereHas('slot', function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId);
            });

        if (! empty($filters['date'])) {
            $query->whereDate(
                'appointment_date',
                $filters['date']
            );
        }

        if (! empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (! empty($filters['patient_id'])) {
            $query->where(
                'patient_id',
                $filters['patient_id']
            );
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query
        ->latest()
        ->paginate($perPage);
    }

    public function details(string $referenceNumber): Appointment
    {
        $appointment = Appointment::query()
            ->with([
                'patient',
                'slot.doctor',
            ])
            ->where('reference_number', $referenceNumber)
            ->first();

        if (! $appointment) {
            throw new Exception('Appointment not found.');
        }

        return $appointment;
    }
}
