<?php

namespace App\Http\Requests\Staff\Timetables;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
{
    private const DAY_OPTIONS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

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
            // Backward-compatible: accept old single day payloads, but prefer day_of_week_list.
            'day_of_week' => ['nullable', 'required_without:day_of_week_list', 'in:'.implode(',', self::DAY_OPTIONS)],
            'day_of_week_list' => ['nullable', 'required_without:day_of_week', 'array', 'min:1'],
            'day_of_week_list.*' => ['string', 'distinct', 'in:'.implode(',', self::DAY_OPTIONS)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'day_of_week_list.required_without' => 'Please select at least one day of the week.',
            'day_of_week.required_without' => 'Please select at least one day of the week.',
            'day_of_week_list.min' => 'Please select at least one day of the week.',
            'day_of_week_list.*.distinct' => 'Duplicate days are not allowed.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $singleDay = $this->input('day_of_week');
        $dayList = $this->input('day_of_week_list');

        if (! is_array($dayList) && is_string($singleDay) && trim($singleDay) !== '') {
            $this->merge([
                'day_of_week_list' => [$singleDay],
            ]);
        }
    }
}
