<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', 'max:10', Rule::exists('locales', 'code')],
            'value' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'exists:tags,slug'],
            'key_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}