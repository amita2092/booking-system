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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();

        $table->string('reference_number')->unique();

        $table->foreignId('doctor_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('patient_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('slot_id')
            ->constrained('appointment_slots')
            ->cascadeOnDelete();

        $table->enum('status', [
            'booked',
            'cancelled',
            'rescheduled'
        ])->default('booked');

        $table->timestamp('booked_at');

        $table->timestamp('cancelled_at')->nullable();

        $table->text('cancellation_reason')->nullable();

        $table->timestamps();

        $table->index('doctor_id');
        $table->index('patient_id');
        $table->index('slot_id');
        $table->index('status');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
