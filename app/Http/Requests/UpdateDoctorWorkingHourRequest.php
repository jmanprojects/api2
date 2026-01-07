<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorWorkingHourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consulting_room_id'      => ['sometimes', 'integer', 'exists:consulting_rooms,id'],
            'weekday'                 => ['sometimes', 'integer', 'min:1', 'max:7'],
            'start_time'              => ['sometimes', 'date_format:H:i'],
            'end_time'                => ['sometimes', 'date_format:H:i'],
            'slot_duration_minutes'   => ['sometimes', 'integer', 'min:5', 'max:240'],
        ];
    }
}
