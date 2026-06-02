<?php

namespace App\Services;

use App\Models\DoctorAvailability;

class AvailabilityService
{
    public function hasOverlap(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime
    ): bool {

        return DoctorAvailability::query()
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
    }
}