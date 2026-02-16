<?php

namespace App\Http\Requests\Staff\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'role' => ['required', 'in:student,teacher,staff'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }
}
