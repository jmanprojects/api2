<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorWorkingHourRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Later: restrict to doctor only (or doctor owner).
        return true;
    }

    public function rules(): array
    {
        return [
            'consulting_room_id'      => ['required', 'integer', 'exists:consulting_rooms,id'],

            // ISO weekday: 1=Mon ... 7=Sun
            'weekday'                 => ['required', 'integer', 'min:1', 'max:7'],

            // "HH:MM" or "HH:MM:SS"
            'start_time'              => ['required', 'date_format:H:i'],
            'end_time'                => ['required', 'date_format:H:i', 'after:start_time'],

            'slot_duration_minutes'   => ['nullable', 'integer', 'min:5', 'max:240'],
        ];
    }
}
