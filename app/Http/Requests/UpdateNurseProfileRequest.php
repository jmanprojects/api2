<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNurseProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // MVP: cualquier usuario autenticado puede completar su nurse profile
        // Si después quieres restringirlo, aquí lo haces.
        return true;
    }

    public function rules(): array
    {
        return [
            // User fields (opcionales)
            'email' => ['nullable', 'email', 'max:255'],

            // Nurse fields (wizard)
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'middle_name'     => ['nullable', 'string', 'max:100'],

            'position'        => ['required', 'string', 'max:120'],
            'license_number'  => ['nullable', 'string', 'max:120'],

            'phone'           => ['required', 'string', 'max:30'],
            'secondary_phone' => ['nullable', 'string', 'max:30'],

            'notes'           => ['nullable', 'string', 'max:2000'],

            // status opcional (si no lo quieres en wizard, déjalo nullable)
            'status'          => ['nullable', 'in:active,inactive'],
        ];
    }
}
