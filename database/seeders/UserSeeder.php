<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $doctorRole = Role::where('name', 'doctor')->first();
        $patientRole = Role::where('name', 'patient')->first();

        // Doctors

        User::insert([
            [
                'role_id' => $doctorRole->id,
                'name' => 'Dr John Smith',
                'email' => 'doctor1@example.com',
                'phone' => '9999999001',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $doctorRole->id,
                'name' => 'Dr Sarah Wilson',
                'email' => 'doctor2@example.com',
                'phone' => '9999999002',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $doctorRole->id,
                'name' => 'Dr David Brown',
                'email' => 'doctor3@example.com',
                'phone' => '9999999003',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Patients

        User::insert([
            [
                'role_id' => $patientRole->id,
                'name' => 'Alice Johnson',
                'email' => 'patient1@example.com',
                'phone' => '8888888001',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $patientRole->id,
                'name' => 'Bob Miller',
                'email' => 'patient2@example.com',
                'phone' => '8888888002',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $patientRole->id,
                'name' => 'Emma Davis',
                'email' => 'patient3@example.com',
                'phone' => '8888888003',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $patientRole->id,
                'name' => 'Michael Lee',
                'email' => 'patient4@example.com',
                'phone' => '8888888004',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $patientRole->id,
                'name' => 'Sophia Clark',
                'email' => 'patient5@example.com',
                'phone' => '8888888005',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
