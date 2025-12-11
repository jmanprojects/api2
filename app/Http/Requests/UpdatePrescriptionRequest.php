<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'],
            'items.*.dose'          => ['nullable', 'string', 'max:255'],
            'items.*.frequency'     => ['nullable', 'string', 'max:255'],
            'items.*.duration'      => ['nullable', 'string', 'max:255'],
            'items.*.route'         => ['nullable', 'string', 'max:255'],
            'items.*.instructions'  => ['nullable', 'string'],
        ];
    }
}
