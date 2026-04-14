<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'exists:tags,slug'],
            'is_approved' => ['nullable', 'boolean'],
        ];
    }
}