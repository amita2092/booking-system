<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DoctorSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $doctor1 = User::where('email', 'doctor1@example.com')->first();
        $doctor2 = User::where('email', 'doctor2@example.com')->first();
        $doctor3 = User::where('email', 'doctor3@example.com')->first();

        DB::table('doctor_specializations')->insert([
            [
                'doctor_id' => $doctor1->id,
                'specialization_id' => Specialization::where('name', 'Cardiology')->value('id'),
            ],
            [
                'doctor_id' => $doctor2->id,
                'specialization_id' => Specialization::where('name', 'Dermatology')->value('id'),
            ],
            [
                'doctor_id' => $doctor3->id,
                'specialization_id' => Specialization::where('name', 'Neurology')->value('id'),
            ],
        ]);
    }
}
