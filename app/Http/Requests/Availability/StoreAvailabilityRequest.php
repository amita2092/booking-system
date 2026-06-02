<?php

namespace App\Http\Requests\Availability;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAvailabilityRequest extends FormRequest
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

            'doctor_id' => [
                'required',
                'exists:users,id'
            ],

            'available_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'required',
                'date_format:H:i'
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time'
            ],

            'slot_duration' => [
                'required',
                'integer',
                'min:5'
            ]
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $timezone = 'Asia/Kolkata';
            $now = Carbon::now($timezone);
            $date = Carbon::parse($this->input('available_date'), $timezone)
                ->startOfDay();
            $startDateTime = Carbon::parse(
                $this->input('available_date') . ' ' . $this->input('start_time'),
                $timezone
            );

            if ($date->lt($now->copy()->startOfDay())) {
                $validator->errors()->add(
                    'available_date',
                    'Past date cannot be added.'
                );

                return;
            }

            if ($startDateTime->lte($now)) {
                $validator->errors()->add(
                    'start_time',
                    'Past time cannot be added.'
                );
            }
        });
    }
}
