<?php

namespace App\Http\Requests\Staff\Timetables;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
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
            'subject_id' => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
