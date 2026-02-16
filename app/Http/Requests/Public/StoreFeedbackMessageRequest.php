<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackMessageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ];

        if (config('recaptcha.site_key')) {
            $rules['recaptcha_token'] = ['required', 'string'];
        }

        return $rules;
    }
}
