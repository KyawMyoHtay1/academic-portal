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
            'feedback' => ['nullable', 'string', 'max:8000'],
            'rubric' => ['nullable', 'array', 'max:12'],
            'rubric.*.criterion' => ['nullable', 'string', 'max:120'],
            'rubric.*.score' => ['nullable', 'numeric', 'min:0'],
            'rubric.*.max_score' => ['nullable', 'numeric', 'min:0.01'],
            'rubric.*.comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}
