<?php

namespace App\Http\Requests\Staff\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:student,teacher,staff'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }
}
