<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Http\Requests\Appointment\BookAppointmentRequest;

class AppointmentController extends Controller
{
    public function store(
        BookAppointmentRequest $request,
        AppointmentService $service
    ): JsonResponse {

        try {

            $appointment =
                $service->book(
                    $request->patient_id,
                    $request->slot_id
                );

            return response()->json([
                'success' => true,
                'message' =>
                    'Appointment booked successfully.',
                'reference_number' =>
                    $appointment->reference_number
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
