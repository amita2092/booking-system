<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'available_date',
        'start_time',
        'end_time',
        'slot_duration',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(
            AppointmentSlot::class,
            'availability_id'
        );
    }
    
}