<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\DoctorAvailability;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(); // load doctors, patients, slots

        $user = User::first();
        Sanctum::actingAs($user);
    }

    public function test_can_book_appointment()
    {
        Event::fake();

        $patient = User::whereHas('role', function ($query) {
            $query->where('name', 'patient');
        })->firstOrFail();

        $slot = $this->createSlot();

        $response = $this->postJson('/api/v1/appointments', [
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
        ]);

        $response->assertStatus(201);

        Event::assertDispatched(AppointmentBooked::class);
    }

    public function test_can_cancel_appointment()
    {
        Event::fake();

        $appointment = $this->createAppointment();

        $response = $this->postJson('/api/v1/appointments/cancel', [
            'reference_number' => $appointment->reference_number,
            'reason' => 'Patient requested cancellation.',
        ]);

        $response->assertStatus(200);

        Event::assertDispatched(AppointmentCancelled::class);
    }

    public function test_can_reschedule_appointment()
    {
        Event::fake();

        $appointment = $this->createAppointment();
        $newSlot = $this->createSlot('available', 2);

        $response = $this->postJson('/api/v1/appointments/reschedule', [
            'reference_number' => $appointment->reference_number,
            'new_slot_id' => $newSlot->id,
        ]);

        $response->assertStatus(200);

        Event::assertDispatched(AppointmentRescheduled::class);
    }

    private function createAvailability(): DoctorAvailability
    {
        $doctor = User::whereHas('role', function ($query) {
            $query->where('name', 'doctor');
        })->firstOrFail();

        return DoctorAvailability::create([
            'doctor_id' => $doctor->id,
            'available_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'slot_duration' => 30,
        ]);
    }

    private function createSlot(string $status = 'available', int $hoursFromStart = 0): AppointmentSlot
    {
        $availability = $this->createAvailability();
        $slotStart = now()
            ->addDay()
            ->setTime(10 + $hoursFromStart, 0);

        return AppointmentSlot::create([
            'doctor_id' => $availability->doctor_id,
            'availability_id' => $availability->id,
            'slot_start' => $slotStart,
            'slot_end' => $slotStart->copy()->addMinutes(30),
            'status' => $status,
        ]);
    }

    private function createAppointment(): Appointment
    {
        $patient = User::whereHas('role', function ($query) {
            $query->where('name', 'patient');
        })->firstOrFail();

        $slot = $this->createSlot('booked');

        return Appointment::create([
            'reference_number' => 'APT-TEST-' . uniqid(),
            'doctor_id' => $slot->doctor_id,
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
            'status' => 'booked',
            'booked_at' => now(),
        ]);
    }
}
