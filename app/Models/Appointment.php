<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'reference_number',
        'doctor_id',
        'patient_id',
        'slot_id',
        'status',
        'booked_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(
            AppointmentSlot::class,
            'slot_id'
        );
    }
}