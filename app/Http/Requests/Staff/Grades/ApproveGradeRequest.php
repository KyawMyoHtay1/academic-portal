<?php

namespace App\Http\Requests\Staff\Grades;

use Illuminate\Foundation\Http\FormRequest;

class ApproveGradeRequest extends FormRequest
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
            'redirect_subject_id' => ['nullable', 'integer'],
        ];
    }
}
