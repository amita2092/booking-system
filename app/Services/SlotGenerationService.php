<?php

namespace App\Services;

class SlotGenerationService
{
    public function generate(
        int $doctorId,
        int $availabilityId,
        string $date,
        string $startTime,
        string $endTime,
        int $duration
    ): array {

        $slots = [];

        $current = strtotime($startTime);

        $end = strtotime($endTime);

        while ($current < $end) {

            $next = $current + ($duration * 60);

            if ($next > $end) {
                break;
            }

            $slots[] = [
                'doctor_id' => $doctorId,
                'availability_id' => $availabilityId,
                'slot_start' => date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $date . ' ' .
                        date('H:i:s', $current)
                    )
                ),
                'slot_end' => date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $date . ' ' .
                        date('H:i:s', $next)
                    )
                ),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $current = $next;
        }

        return $slots;
    }
}