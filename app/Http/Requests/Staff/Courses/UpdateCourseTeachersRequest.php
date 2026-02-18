<?php

namespace App\Http\Requests\Staff\Courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseTeachersRequest extends FormRequest
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
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where('role', 'teacher'),
            ],
        ];
    }
}
