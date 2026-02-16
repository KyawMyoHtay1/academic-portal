<?php

namespace App\Http\Requests\StudentProfile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
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
            'phone' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
