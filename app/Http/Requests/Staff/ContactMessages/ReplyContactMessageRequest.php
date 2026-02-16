<?php

namespace App\Http\Requests\Staff\ContactMessages;

use Illuminate\Foundation\Http\FormRequest;

class ReplyContactMessageRequest extends FormRequest
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
            'reply' => ['required', 'string', 'max:5000'],
        ];
    }
}
