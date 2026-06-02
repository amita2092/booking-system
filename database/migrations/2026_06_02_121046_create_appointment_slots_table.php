<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_slots', function (Blueprint $table) {

            $table->id();

            $table->foreignId('doctor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('availability_id')
                ->constrained('doctor_availabilities')
                ->cascadeOnDelete();

            $table->dateTime('slot_start');

            $table->dateTime('slot_end');

            $table->enum('status', [
                'available',
                'booked',
                'blocked',
                'expired'
            ])->default('available');

            $table->timestamps();

            $table->unique([
                'doctor_id',
                'slot_start'
            ]);

            $table->index('status');

            $table->index('slot_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
