<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Http\Requests\Appointment\BookAppointmentRequest;
use App\Http\Requests\Appointment\CancelAppointmentRequest;
use App\Http\Requests\Appointment\RescheduleAppointmentRequest;
use App\Http\Requests\Appointment\DoctorAppointmentListRequest;

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

    public function cancel(
        CancelAppointmentRequest $request,
        AppointmentService $service
    )
    {
        try {

            $appointment = $service->cancel(
                $request->reference_number,
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully.',
                'reference_number' => $appointment->reference_number
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function reschedule(
        RescheduleAppointmentRequest $request,
        AppointmentService $service
    ) {
        try {

            $appointment = $service->reschedule(
                $request->reference_number,
                $request->new_slot_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully.',
                'reference_number' => $appointment->reference_number
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function doctorAppointments(
        DoctorAppointmentListRequest $request,
        AppointmentService $service
    ): JsonResponse {

        try {
            $filters = $request->validated();

            $appointments = $service->doctorAppointments(
                (int) $filters['doctor_id'],
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $appointments->items(),
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                    'last_page' => $appointments->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function details(
        Request $request,
        AppointmentService $service
    ): JsonResponse {

        $request->validate([
            'reference_number' => ['required', 'string'],
        ]);

        try {

            $appointment = $service->details(
                $request->reference_number
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'reference_number' => $appointment->reference_number,
                    'status' => $appointment->status,
                    'patient' => $appointment->patient,
                    'doctor' => $appointment->slot->doctor,
                    'slot' => $appointment->slot,
                    'cancel_reason' => $appointment->cancel_reason,
                    'created_at' => $appointment->created_at,
                ]
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
