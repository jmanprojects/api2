<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNurseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Later you can restrict this so only doctors/owners can create nurses.
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],

            'position'    => ['nullable', 'string', 'max:100'],

            'phone'           => ['nullable', 'string', 'max:50'],
            'secondary_phone' => ['nullable', 'string', 'max:50'],

            'license_number'  => ['nullable', 'string', 'max:255'],

            'notes'  => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],

            // Optional email for the underlying user
            'email'          => ['nullable', 'email', 'max:255'],
        ];
    }
}
