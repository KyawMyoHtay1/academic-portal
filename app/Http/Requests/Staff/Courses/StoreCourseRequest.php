<?php

namespace App\Http\Requests\Staff\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'course_code' => ['required', 'string', 'max:50', 'unique:courses,course_code'],
            'title' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }
}
