<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Later you can restrict to nurse/doctor only
        return true;
    }

    public function rules(): array
    {
        return [
            'height'                    => ['nullable', 'numeric', 'min:0'],
            'weight'                    => ['nullable', 'numeric', 'min:0'],
            'temperature'               => ['nullable', 'numeric', 'min:30', 'max:45'],
            'blood_pressure_systolic'   => ['nullable', 'integer', 'min:50', 'max:250'],
            'blood_pressure_diastolic'  => ['nullable', 'integer', 'min:30', 'max:150'],
            'heart_rate'                => ['nullable', 'integer', 'min:20', 'max:250'],
            'respiratory_rate'          => ['nullable', 'integer', 'min:5', 'max:80'],
            'oxygen_saturation'         => ['nullable', 'integer', 'min:50', 'max:100'],
            'blood_glucose'             => ['nullable', 'numeric', 'min:0'],
            'pain_scale'                => ['nullable', 'integer', 'min:0', 'max:10'],
            'notes'                     => ['nullable', 'string'],
        ];
    }
}
