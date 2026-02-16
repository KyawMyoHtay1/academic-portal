<?php

namespace App\Http\Requests\Staff\Courses;

use Illuminate\Foundation\Http\FormRequest;

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
            'teacher_ids.*' => ['exists:users,id'],
        ];
    }
}
