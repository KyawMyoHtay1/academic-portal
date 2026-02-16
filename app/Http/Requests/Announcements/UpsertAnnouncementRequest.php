<?php

namespace App\Http\Requests\Announcements;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'priority' => ['required', 'in:info,important,urgent'],
            'pinned' => ['nullable', 'boolean'],
            'require_ack' => ['nullable', 'boolean'],
            'audience' => ['nullable', 'array'],
            'audience.roles' => ['nullable', 'array'],
            'audience.roles.*' => ['in:all,student,teacher,staff'],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:publish_at'],
        ];
    }
}
