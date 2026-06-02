<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'doctor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'patient',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
