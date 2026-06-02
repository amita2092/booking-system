<?php

namespace App\Services;

use Exception;
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

        return DB::transaction(function () use (
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
    }
}