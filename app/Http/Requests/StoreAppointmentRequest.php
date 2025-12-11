<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // más adelante puedes limitar a doctor/nurse
    }

    public function rules(): array
    {
        return [
            'patient_id'         => ['required', 'integer', 'exists:patients,id'],
            'consulting_room_id' => ['required', 'integer', 'exists:consulting_rooms,id'],
            'scheduled_at'       => ['required', 'date'],
            'duration_minutes'   => ['nullable', 'integer', 'min:5', 'max:240'],
            'type'               => ['nullable', 'string', 'max:50'],
            'source'             => ['nullable', 'string', 'max:50'],
            'reason'             => ['nullable', 'string', 'max:255'],
        ];
    }
}
