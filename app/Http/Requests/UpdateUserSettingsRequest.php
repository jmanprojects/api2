<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
{
    /**
     * Authorization layer.
     *
     * We keep this "true" because we already protect the route with auth:sanctum.
     * If later you need role-based restrictions (doctor only, etc.), you can enforce
     * that with middleware or Policies, without changing this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     *
     * IMPORTANT:
     * - We validate ONLY the settings we support.
     * - This prevents random payload keys from being written to the user model.
     *
     * "system" is included as a professional option:
     * The front can decide dark/light based on OS preference.
     */
    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ];
    }
}

