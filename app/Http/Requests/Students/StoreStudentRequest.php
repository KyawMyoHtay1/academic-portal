<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'student');
                }),
                'unique:students,user_id',
            ],
            'student_no' => ['nullable', 'string', 'max:50', 'unique:students,student_no'],
            'full_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'programme' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'id_card' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'transcript' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'previous_institution' => ['required', 'string', 'max:255'],
            'previous_qualification' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,suspended,graduated'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected linked user must be a student account.',
        ];
    }
}
