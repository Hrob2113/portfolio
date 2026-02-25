<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'translations' => ['required', 'array'],
            'translations.*.id' => ['required', 'integer', 'exists:translations,id'],
            'translations.*.value' => ['required', 'string'],
        ];
    }
}
