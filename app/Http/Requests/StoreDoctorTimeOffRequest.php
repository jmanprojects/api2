<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorTimeOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Optional: scope to a specific consulting room; null means "all rooms"
            'consulting_room_id' => ['nullable', 'integer', 'exists:consulting_rooms,id'],

            'start_datetime'     => ['required', 'date'],
            'end_datetime'       => ['required', 'date', 'after:start_datetime'],

            'reason'             => ['nullable', 'string', 'max:255'],
        ];
    }
}
