<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // You can later enforce that only doctors/nurses can create patients.
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:255'],

            'gender'       => ['nullable', 'string', 'max:20'],
            'birth_date'   => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'occupation'   => ['nullable', 'string', 'max:100'],

            'phone'            => ['nullable', 'string', 'max:50'],
            'secondary_phone'  => ['nullable', 'string', 'max:50'],
            'alternate_email'  => ['nullable', 'email', 'max:255'],
            'email'            => ['nullable', 'email', 'max:255'], // for user email

            'document_type'    => ['nullable', 'string', 'max:50'],
            'document_number'  => ['nullable', 'string', 'max:100'],

            'blood_type'       => ['nullable', 'string', 'max:10'],
            'allergies'        => ['nullable', 'string'],
            'chronic_conditions' => ['nullable', 'string'],

            'emergency_contact_name'  => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],

            'notes'           => ['nullable', 'string'],
            'status'          => ['nullable', 'string', 'max:50'],
        ];
    }
}
