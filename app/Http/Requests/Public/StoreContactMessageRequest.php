<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
        ];

        $shouldValidateRecaptcha = ! app()->environment('local')
            && filled(config('recaptcha.site_key'))
            && filled(config('recaptcha.secret_key'));

        if ($shouldValidateRecaptcha) {
            $rules['recaptcha_token'] = ['required', 'string'];
        }

        return $rules;
    }
}
