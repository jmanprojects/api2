<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic user info
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],

            // Doctor fields
            'professional_license' => ['nullable', 'string', 'max:255'],
            'specialty'            => ['nullable', 'string', 'max:255'],
            'secondary_specialty'  => ['nullable', 'string', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:50'],
            'gender'               => ['nullable', 'string', 'max:20'],
            'birth_date'           => ['nullable', 'date'],
            'bio'                  => ['nullable', 'string'],
            'status'               => ['nullable', 'string', 'max:50'],
            'photo_path'           => ['nullable', 'string', 'max:255'],

            // ✅ Wizard requirement: consultorio name
            // Si quieres permitir updates sin esto en el futuro, cámbialo a "sometimes|..."
            'consultorio_name'      => ['required', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'professional_license' => 'professional license',
            'consultorio_name'     => 'consultorio name',
        ];
    }
}
