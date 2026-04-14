<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkCreateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1', 'max:1000'],
            'translations.*.key' => ['required', 'string', 'max:255'],
            'translations.*.locale' => ['required', 'string', 'max:10', Rule::exists('locales', 'code')],
            'translations.*.value' => ['required', 'string'],
            'translations.*.tags' => ['nullable', 'array'],
            'translations.*.tags.*' => ['string', 'exists:tags,slug'],
        ];
    }
}