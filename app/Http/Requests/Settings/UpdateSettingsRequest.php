<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email_announcements' => ['sometimes', 'boolean'],
            'email_messages' => ['sometimes', 'boolean'],
            'email_notifications' => ['sometimes', 'boolean'],
        ];
    }
}
