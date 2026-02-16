<?php

namespace App\Http\Requests\Teacher\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'max_score' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', 'in:draft,published,closed'],
            'allowed_file_types' => ['nullable', 'array'],
            'allowed_file_types.*' => ['string', 'in:pdf,doc,docx,txt,zip,rar'],
            'max_file_size' => ['nullable', 'integer', 'min:1', 'max:10240'],
        ];
    }
}
