<?php

namespace App\Http\Controllers;

use App\Jobs\SendLowAttendanceAlertsJob;
use App\Services\AttendanceReports\LowAttendanceAlertTargetResolver;
use App\Services\AttendanceReports\StaffAttendanceReportFiltersResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffAttendanceAlertsController extends Controller
{
    public function __construct(
        private readonly StaffAttendanceReportFiltersResolver $filtersResolver,
        private readonly LowAttendanceAlertTargetResolver $lowAttendanceAlertTargetResolver,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'threshold' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'cooldown_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'bypass_cooldown' => ['sometimes', 'boolean'],
        ]);

        $hasThresholdOverride = array_key_exists('threshold', $data);
        $hasCooldownOverride = array_key_exists('cooldown_days', $data);
        $bypassCooldown = (bool) ($data['bypass_cooldown'] ?? ! $hasCooldownOverride);

        // Manual runs should be able to resend alerts immediately from the dashboard.
        // The scheduled console command still uses the configured cooldown.
        $cooldownDays = $hasCooldownOverride ? (int) $data['cooldown_days'] : 0;

        $resolved = $this->filtersResolver->resolve($request);
        $candidateRows = $this->lowAttendanceAlertTargetResolver->buildRows(
            $resolved['filters'],
            $resolved['options']['courses'],
            $resolved['options']['subjects']
        );

        if ($candidateRows->isEmpty()) {
            return back()->with('info', 'No students are currently below the attendance threshold for the selected filters.');
        }

        $previewRows = $this->lowAttendanceAlertTargetResolver->annotateDispatchability(
            $candidateRows,
            $cooldownDays,
            $bypassCooldown
        );

        $dispatchableRows = $previewRows
            ->filter(fn (array $row) => $row['should_alert'])
            ->values();

        if ($dispatchableRows->isEmpty()) {
            return back()->with('info', 'No eligible low attendance recipients currently need an alert for the selected filters.');
        }

        $effectiveThreshold = (float) ($dispatchableRows->first()['threshold'] ?? ($hasThresholdOverride ? (float) $data['threshold'] : 75));

        SendLowAttendanceAlertsJob::dispatch(
            $effectiveThreshold,
            $cooldownDays,
            $this->lowAttendanceAlertTargetResolver->buildJobPayload($dispatchableRows)
        );

        $parts = [];
        if ($hasThresholdOverride) {
            $parts[] = sprintf('threshold %.2f%%', $effectiveThreshold);
        }
        if ($hasCooldownOverride) {
            $parts[] = sprintf('cooldown %d day(s)', $cooldownDays);
        }
        $suffix = $parts === [] ? '' : ' ('.implode(', ', $parts).')';

        $alertCount = $dispatchableRows->count();
        $emailCount = $dispatchableRows->where('should_email', true)->count();

        $message = sprintf(
            'Queued %d low attendance alert(s)%s.',
            $alertCount,
            $suffix
        );

        if ($emailCount > 0) {
            $message .= sprintf(' %d will also be sent by email.', $emailCount);
        }

        if ($bypassCooldown) {
            $message .= ' Manual run bypassed cooldown.';
        }

        return back()->with('success', $message);
    }
}
