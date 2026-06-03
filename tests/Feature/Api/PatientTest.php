<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::first();
        Sanctum::actingAs($user);
    }

    public function test_can_fetch_patients()
    {
        $response = $this->getJson('/api/v1/patients');

        $response->assertStatus(200);
    }
}
