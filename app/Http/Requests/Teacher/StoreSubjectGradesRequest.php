<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectGradesRequest extends FormRequest
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
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'grades.required' => 'No student grade entries were provided.',
            'grades.array' => 'Invalid grade payload format.',
            'grades.*.student_id.required' => 'Each grade row must include a student.',
            'grades.*.student_id.exists' => 'One or more selected students do not exist.',
            'grades.*.score.numeric' => 'Score must be a valid number.',
            'grades.*.score.min' => 'Score must be between 0 and 100.',
            'grades.*.score.max' => 'Score must be between 0 and 100.',
        ];
    }
}
