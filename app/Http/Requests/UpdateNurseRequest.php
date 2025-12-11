<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNurseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'  => ['sometimes', 'string', 'max:255'],
            'last_name'   => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],

            'position'    => ['sometimes', 'nullable', 'string', 'max:100'],

            'phone'           => ['sometimes', 'nullable', 'string', 'max:50'],
            'secondary_phone' => ['sometimes', 'nullable', 'string', 'max:50'],

            'license_number'  => ['sometimes', 'nullable', 'string', 'max:255'],

            'notes'  => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],

            'email'          => ['sometimes', 'nullable', 'email', 'max:255'],
        ];
    }
}
