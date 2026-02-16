<?php

namespace App\Http\Requests\Teacher\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => ['required', 'in:present,absent'],
        ];
    }
}
