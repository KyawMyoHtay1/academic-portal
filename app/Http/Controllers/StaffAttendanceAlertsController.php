<?php

namespace App\Http\Controllers;

use App\Jobs\SendLowAttendanceAlertsJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffAttendanceAlertsController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'threshold' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'cooldown_days' => ['nullable', 'integer', 'min:0', 'max:90'],
        ]);

        $threshold = array_key_exists('threshold', $data) ? (float) $data['threshold'] : null;
        $cooldownDays = array_key_exists('cooldown_days', $data) ? (int) $data['cooldown_days'] : null;

        SendLowAttendanceAlertsJob::dispatch($threshold, $cooldownDays);

        $parts = [];
        if ($threshold !== null) {
            $parts[] = sprintf('threshold %.2f%%', $threshold);
        }
        if ($cooldownDays !== null) {
            $parts[] = sprintf('cooldown %d day(s)', $cooldownDays);
        }
        $suffix = $parts === [] ? '' : ' ('.implode(', ', $parts).')';

        return back()->with('success', 'Low attendance alerts have been queued'.$suffix.'.');
    }
}
