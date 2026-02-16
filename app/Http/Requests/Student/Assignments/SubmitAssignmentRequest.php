<?php

namespace App\Http\Requests\Student\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
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
            'file' => ['required', 'file'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
