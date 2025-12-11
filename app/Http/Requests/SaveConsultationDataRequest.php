<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveConsultationDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Later restrict to doctor only
        return true;
    }

    public function rules(): array
    {
        return [
            'chief_complaint' => ['nullable', 'string'],
            'subjective'      => ['nullable', 'string'],
            'objective'       => ['nullable', 'string'],
            'assessment'      => ['nullable', 'string'],
            'plan'            => ['nullable', 'string'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}
