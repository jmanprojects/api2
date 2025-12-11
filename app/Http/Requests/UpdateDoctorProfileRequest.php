<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Here we usually check if the authenticated user is allowed
     * to update this doctor profile.
     *
     * For now, we simply return true and push authorization logic
     * into policies or controller/service level.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * These rules ensure that only clean and valid data
     * is accepted when updating a doctor profile.
     */
    public function rules(): array
    {
        return [
            // Basic user info (we will update through the doctor->user relation)
            'name' => ['sometimes', 'string', 'max:255'],
            // You might decide not to allow email changes here
            'email' => ['sometimes', 'email', 'max:255'],

            // Doctor specific fields
            'professional_license'   => ['nullable', 'string', 'max:255'],
            'specialty'              => ['nullable', 'string', 'max:255'],
            'secondary_specialty'    => ['nullable', 'string', 'max:255'],
            'phone'                  => ['nullable', 'string', 'max:50'],
            'gender'                 => ['nullable', 'string', 'max:20'],
            'birth_date'             => ['nullable', 'date'],
            'bio'                    => ['nullable', 'string'],
            'status'                 => ['nullable', 'string', 'max:50'],

            // For photo we only validate metadata here; actual file upload
            // handling might be done in a different endpoint.
            'photo_path'             => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Customize attribute names or messages if needed.
     * Keeping it simple for now.
     */
    public function attributes(): array
    {
        return [
            'professional_license' => 'professional license',
        ];
    }
}
