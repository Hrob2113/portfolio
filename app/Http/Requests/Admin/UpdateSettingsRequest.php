<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'settings' => ['required', 'array'],
            'settings.github_url' => ['nullable', 'url', 'max:255'],
            'settings.linkedin_url' => ['nullable', 'url', 'max:255'],
            'settings.dribbble_url' => ['nullable', 'url', 'max:255'],
            'settings.twitter_url' => ['nullable', 'url', 'max:255'],
            'settings.contact_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
