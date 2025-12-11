<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ya está autenticado con Sanctum
    }

    public function rules(): array
    {
        return [
            // Datos personales del médico
            'name'                => ['required', 'string', 'max:255'],
            'last_name'           => ['required', 'string', 'max:255'],
            'degree'              => ['required', 'string'],
            'speciality'          => ['required', 'string'],
            'professional_license'=> ['required', 'string'],

            // Foto de usuario
            'photo'               => ['nullable', 'image', 'max:4096'],

            // Datos del consultorio (EN LA MISMA TABLA DOCTOR)
            'clinic_name' => ['required', 'string'],
            'address'     => ['required', 'string'],
            'phone'       => ['required', 'regex:/^[0-9]{10}$/'],

            // Logo consultorio
            'clinic_logo' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
