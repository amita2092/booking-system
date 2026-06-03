<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reference_number' => [
                'required',
                'exists:appointments,reference_number'
            ],
            'new_slot_id' => [
                'required',
                'exists:appointment_slots,id'
            ]
        ];
    }
}
