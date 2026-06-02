<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    protected $fillable = [
        'doctor_id',
        'availability_id',
        'slot_start',
        'slot_end',
        'status',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(
            DoctorAvailability::class,
            'availability_id'
        );
    }
}