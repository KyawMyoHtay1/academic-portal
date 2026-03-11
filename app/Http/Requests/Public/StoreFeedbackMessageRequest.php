<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $feedbackTypes = ['suggestion', 'issue', 'compliment', 'other'];

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'string', Rule::in($feedbackTypes)],
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
