<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Support\AttendanceAlertSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Default preference keys and values.
     */
    public const DEFAULTS = [
        'email_announcements' => true,
        'email_messages' => true,
        'email_notifications' => true,
        'notify_timetable' => true,
        'notify_attendance' => true,
        'notify_grades' => true,
        'notify_grade_review' => true,
        'notify_fees' => true,
        'notify_messages' => true,
    ];

    /**
     * Display the settings page for the authenticated user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $preferences = array_merge(self::DEFAULTS, $user->preferences ?? []);
        $canManageAttendanceAlertDefaults = in_array((string) $user->role, ['staff', 'admin'], true);

        return Inertia::render('Settings/Index', [
            'role' => $user->role,
            'preferences' => $preferences,
            'attendanceAlerts' => [
                'low_threshold' => AttendanceAlertSettings::lowThreshold(),
                'cooldown_days' => AttendanceAlertSettings::cooldownDays(),
                'can_manage_defaults' => $canManageAttendanceAlertDefaults,
            ],
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's settings/preferences.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = Auth::user();
        $preferenceValues = array_intersect_key($validated, self::DEFAULTS);
        $preferences = array_merge(self::DEFAULTS, $user->preferences ?? [], $preferenceValues);
        $user->update(['preferences' => $preferences]);

        if (in_array((string) $user->role, ['staff', 'admin'], true)) {
            if (array_key_exists('attendance_low_threshold', $validated)) {
                AttendanceAlertSettings::setLowThreshold((float) $validated['attendance_low_threshold'], $user->id);
            }
            if (array_key_exists('attendance_cooldown_days', $validated)) {
                AttendanceAlertSettings::setCooldownDays((int) $validated['attendance_cooldown_days'], $user->id);
            }
        }

        return redirect()->route('settings.index')->with('status', 'Settings saved.');
    }
}
