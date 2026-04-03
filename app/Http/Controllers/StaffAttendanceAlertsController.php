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

        $hasThresholdOverride = array_key_exists('threshold', $data);
        $hasCooldownOverride = array_key_exists('cooldown_days', $data);

        $threshold = $hasThresholdOverride ? (float) $data['threshold'] : null;

        // Manual runs should be able to resend alerts immediately from the dashboard.
        // The scheduled console command still uses the configured cooldown.
        $cooldownDays = $hasCooldownOverride ? (int) $data['cooldown_days'] : 0;

        SendLowAttendanceAlertsJob::dispatch($threshold, $cooldownDays);

        $parts = [];
        if ($hasThresholdOverride) {
            $parts[] = sprintf('threshold %.2f%%', $threshold);
        }
        if ($hasCooldownOverride) {
            $parts[] = sprintf('cooldown %d day(s)', $cooldownDays);
        }
        $suffix = $parts === [] ? '' : ' ('.implode(', ', $parts).')';

        $message = 'Low attendance alerts have been sent'.$suffix.'.';

        if (! $hasCooldownOverride) {
            $message .= ' Manual runs bypass cooldown.';
        }

        return back()->with('success', $message);
    }
}
