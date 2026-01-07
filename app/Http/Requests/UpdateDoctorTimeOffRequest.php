<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorTimeOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consulting_room_id' => ['sometimes', 'nullable', 'integer', 'exists:consulting_rooms,id'],
            'start_datetime'     => ['sometimes', 'date'],
            'end_datetime'       => ['sometimes', 'date'],
            'reason'             => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
