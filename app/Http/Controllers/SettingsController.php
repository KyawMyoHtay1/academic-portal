<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingsRequest;
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
    ];

    /**
     * Display the settings page for the authenticated user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $preferences = array_merge(self::DEFAULTS, $user->preferences ?? []);

        return Inertia::render('Settings/Index', [
            'role' => $user->role,
            'preferences' => $preferences,
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
        $preferences = array_merge(self::DEFAULTS, $user->preferences ?? [], $validated);
        $user->update(['preferences' => $preferences]);

        return redirect()->route('settings.index')->with('status', 'Settings saved.');
    }
}
