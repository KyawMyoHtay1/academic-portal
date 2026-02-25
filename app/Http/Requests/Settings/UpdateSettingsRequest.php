<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
        $rules = [
            'email_announcements' => ['sometimes', 'boolean'],
            'email_messages' => ['sometimes', 'boolean'],
            'email_notifications' => ['sometimes', 'boolean'],
            'notify_timetable' => ['sometimes', 'boolean'],
            'notify_attendance' => ['sometimes', 'boolean'],
            'notify_grades' => ['sometimes', 'boolean'],
            'notify_grade_review' => ['sometimes', 'boolean'],
            'notify_fees' => ['sometimes', 'boolean'],
            'notify_messages' => ['sometimes', 'boolean'],
            'notify_assignments' => ['sometimes', 'boolean'],
            'notify_announcements' => ['sometimes', 'boolean'],
        ];

        $role = (string) ($this->user()?->role ?? '');
        $canManageAttendanceAlertDefaults = in_array($role, ['staff', 'admin'], true);

        if ($canManageAttendanceAlertDefaults) {
            $rules['attendance_low_threshold'] = ['sometimes', 'numeric', 'min:1', 'max:100'];
            $rules['attendance_cooldown_days'] = ['sometimes', 'integer', 'min:0', 'max:90'];
        } else {
            $rules['attendance_low_threshold'] = ['prohibited'];
            $rules['attendance_cooldown_days'] = ['prohibited'];
        }

        return $rules;
    }
}
