<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class DoctorAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(); // IMPORTANT: uses your seeders

        $user = User::first(); // from seeder
        Sanctum::actingAs($user);
    }

    public function test_can_fetch_doctor_availabilities()
    {
        $response = $this->getJson('/api/v1/doctors/availabilities');

        $response->assertStatus(200);
    }

    public function test_can_create_doctor_availability()
    {
        $doctor = User::whereHas('role', function ($query) {
            $query->where('name', 'doctor');
        })->firstOrFail();

        $response = $this->postJson('/api/v1/createAvailability', [
            'doctor_id' => $doctor->id,
            'available_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'slot_duration' => 30,
        ]);

        $response->assertStatus(201);
    }
}
