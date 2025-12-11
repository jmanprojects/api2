<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // For updates, everything is optional (sometimes).
        return [
            'first_name'   => ['sometimes', 'string', 'max:255'],
            'last_name'    => ['sometimes', 'string', 'max:255'],
            'middle_name'  => ['sometimes', 'nullable', 'string', 'max:255'],

            'gender'       => ['sometimes', 'nullable', 'string', 'max:20'],
            'birth_date'   => ['sometimes', 'nullable', 'date'],
            'marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'occupation'   => ['sometimes', 'nullable', 'string', 'max:100'],

            'phone'            => ['sometimes', 'nullable', 'string', 'max:50'],
            'secondary_phone'  => ['sometimes', 'nullable', 'string', 'max:50'],
            'alternate_email'  => ['sometimes', 'nullable', 'email', 'max:255'],
            'email'            => ['sometimes', 'nullable', 'email', 'max:255'],

            'document_type'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'document_number'  => ['sometimes', 'nullable', 'string', 'max:100'],

            'blood_type'       => ['sometimes', 'nullable', 'string', 'max:10'],
            'allergies'        => ['sometimes', 'nullable', 'string'],
            'chronic_conditions' => ['sometimes', 'nullable', 'string'],

            'emergency_contact_name'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['sometimes', 'nullable', 'string', 'max:50'],

            'notes'           => ['sometimes', 'nullable', 'string'],
            'status'          => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}
