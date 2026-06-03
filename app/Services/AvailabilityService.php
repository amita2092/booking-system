<?php

namespace App\Services;

use App\Models\AppointmentSlot;
use App\Models\DoctorAvailability;
use Carbon\Carbon;

class AvailabilityService
{
    public function hasOverlap(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime
    ): bool {

        $hasAvailabilityOverlap = DoctorAvailability::query()
            ->where('doctor_id', $doctorId)
            ->where('available_date', $date)
            ->where(function ($query) use (
                $startTime,
                $endTime
            ) {

                $query->where(
                    'start_time',
                    '<',
                    $endTime
                )->where(
                    'end_time',
                    '>',
                    $startTime
                );
            })
            ->exists();

        if ($hasAvailabilityOverlap) {
            return true;
        }

        $slotStart = Carbon::parse($date . ' ' . $startTime);
        $slotEnd = Carbon::parse($date . ' ' . $endTime);

        return AppointmentSlot::query()
            ->where('doctor_id', $doctorId)
            ->where('slot_start', '<', $slotEnd)
            ->where('slot_end', '>', $slotStart)
            ->exists();
    }
}
