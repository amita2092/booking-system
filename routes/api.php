<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use App\Http\Controllers\Api\PatientController;

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {

//     Route::post('/availability', [AvailabilityController::class, 'store']);

//     Route::get('/slots', [AppointmentController::class, 'slots']);

//     Route::post('/appointments', [AppointmentController::class, 'book']);

//     Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);

//     Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule']);
// });

Route::prefix('v1')->group(function () {

    Route::get(
        '/doctors/availabilities',
        [DoctorAvailabilityController::class, 'index']
    );

    Route::post(
        '/createAvailability',
        [DoctorAvailabilityController::class, 'store']
    );

    Route::post(
        '/appointments',
        [AppointmentController::class, 'store']
    );

    Route::get(
        '/patients',
        [PatientController::class, 'index']
    );

});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API Working'
    ]);
});