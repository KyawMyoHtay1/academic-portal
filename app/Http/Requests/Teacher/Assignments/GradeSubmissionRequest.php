<?php

namespace App\Http\Requests\Teacher\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
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
            'score' => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
