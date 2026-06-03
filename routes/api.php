<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use App\Http\Controllers\Api\PatientController;

Route::prefix('v1')->group(function () {

    // Public route
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/doctors/availabilities', [DoctorAvailabilityController::class, 'index']);

        Route::post('/createAvailability', [DoctorAvailabilityController::class, 'store']);

        Route::post('/appointments', [AppointmentController::class, 'store']);

        Route::get('/patients', [PatientController::class, 'index']);

        Route::post('/appointments/cancel', [AppointmentController::class, 'cancel']);

        Route::post('/appointments/doctorAppointments', [AppointmentController::class, 'doctorAppointments']);

        Route::post('/appointments/details', [AppointmentController::class, 'details']);

        Route::post('/appointments/reschedule', [AppointmentController::class, 'reschedule']);
    });
});


    // Route::get('/test', function () {
//     return response()->json([
//         'message' => 'API Working'
//     ]);
// });