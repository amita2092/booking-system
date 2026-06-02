<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\AppointmentSlot;
use App\Models\DoctorAvailability;
use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use App\Services\SlotGenerationService;
use App\Http\Requests\Availability\StoreAvailabilityRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{

    public function index(Request $request)
    {
        $query = DoctorAvailability::query()
            ->with([
                'doctor' => function ($query) {
                    $query->select(
                        'id',
                        'name',
                        'email',
                        'phone'
                    );
                },
                'slots' => function ($query) {
                    $query->where('status', 'available')
                        ->select(
                            'id',
                            'doctor_id',
                            'availability_id',
                            'slot_start',
                            'slot_end',
                            'status'
                        );
                }
            ]);

        if ($request->filled('doctor_id')) {
            $query->where(
                'doctor_id',
                $request->doctor_id
            );
        }

        if ($request->filled('available_date')) {
            $query->where(
                'available_date',
                $request->available_date
            );
        }

        $availabilities = $query
            ->orderBy('available_date')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $availabilities->count(),
            'data' => $availabilities
        ]);
    }

    public function store(
        StoreAvailabilityRequest $request,
        AvailabilityService $availabilityService,
        SlotGenerationService $slotService
    ) {

        $data = $request->validated();

        $doctor = User::findOrFail(
            $data['doctor_id']
        );

        if (
            $doctor->role->name !== 'doctor'
        ) {
            return response()->json([
                'message' =>
                    'Selected user is not a doctor.'
            ], 422);
        }

        if (
            $availabilityService->hasOverlap(
                $data['doctor_id'],
                $data['available_date'],
                $data['start_time'],
                $data['end_time']
            )
        ) {
            return response()->json([
                'message' =>
                    'Availability overlaps with existing schedule.'
            ], 422);
        }

        return DB::transaction(
            function () use (
                $data,
                $slotService
            ) {

                $availability =
                    DoctorAvailability::create($data);

                $slots =
                    $slotService->generate(
                        $availability->doctor_id,
                        $availability->id,
                        $availability->available_date,
                        $availability->start_time,
                        $availability->end_time,
                        $availability->slot_duration
                    );

                AppointmentSlot::insert($slots);

                return response()->json([
                    'success' => true,
                    'message' =>
                        'Availability created successfully.',
                    'slots_generated' =>
                        count($slots)
                ], 201);
            }
        );
    }
}
