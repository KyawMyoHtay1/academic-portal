<?php

namespace App\Jobs;

use App\Models\LowAttendanceAlertState;
use App\Models\Student;
use App\Notifications\LowAttendanceAlert;
use App\Support\AttendanceAlertSettings;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendLowAttendanceAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly ?float $thresholdOverride = null,
        private readonly ?int $cooldownDaysOverride = null
    ) {}

    /**
     * Number of attempts before failing permanently.
     */
    public int $tries = 5;

    /**
     * Maximum unhandled exceptions before failing.
     */
    public int $maxExceptions = 3;

    /**
     * Retry backoff in seconds.
     *
     * @var array<int>
     */
    public array $backoff = [60, 300, 900, 1800];

    /**
     * Timeout in seconds for a single attempt.
     */
    public int $timeout = 120;

    public function handle(): void
    {
        $threshold = $this->thresholdOverride ?? AttendanceAlertSettings::lowThreshold();
        $cooldownDays = $this->cooldownDaysOverride ?? AttendanceAlertSettings::cooldownDays();
        $threshold = max(1, min(100, (float) $threshold));
        $cooldownDays = max(0, min(90, (int) $cooldownDays));
        $now = now();

        DB::table('attendances')
            ->select([
                'student_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present"),
            ])
            ->groupBy('student_id')
            ->orderBy('student_id')
            ->chunk(500, function ($rows) use ($threshold, $cooldownDays, $now) {
                $studentIds = collect($rows)->pluck('student_id')->all();

                $studentsById = Student::query()
                    ->whereIn('id', $studentIds)
                    ->with('user')
                    ->get()
                    ->keyBy('id');

                $statesByStudentId = LowAttendanceAlertState::query()
                    ->whereIn('student_id', $studentIds)
                    ->get()
                    ->keyBy('student_id');

                foreach ($rows as $row) {
                    $total = (int) ($row->total ?? 0);
                    $present = (int) ($row->present ?? 0);
                    if ($total <= 0) {
                        continue;
                    }

                    $rate = round(($present / $total) * 100, 2);
                    $isBelow = $rate < $threshold;

                    $studentId = (int) $row->student_id;
                    $student = $studentsById->get($studentId);
                    $state = $statesByStudentId->get($studentId);

                    $wasBelow = (bool) ($state?->is_below_threshold ?? false);
                    $newlyBelow = $isBelow && ! $wasBelow;

                    $cooldownOk = true;
                    if ($state?->last_alert_sent_at) {
                        $cooldownOk = $state->last_alert_sent_at->lte($now->copy()->subDays($cooldownDays));
                    }

                    // Alert if: (1) newly dropped below threshold, OR (2) still below and cooldown passed
                    $shouldAlert = ($newlyBelow || ($isBelow && $cooldownOk)) && $student && $student->user;

                    $lastAlertSentAt = $state?->last_alert_sent_at;
                    if ($shouldAlert) {
                        $student->user->notify(new LowAttendanceAlert($student, $rate, $threshold));
                        $lastAlertSentAt = $now;
                    }

                    LowAttendanceAlertState::updateOrCreate(
                        ['student_id' => $studentId],
                        [
                            'last_rate' => $rate,
                            'is_below_threshold' => $isBelow,
                            'last_alert_sent_at' => $lastAlertSentAt,
                        ]
                    );
                }
            });
    }

    public function retryUntil(): DateTimeInterface
    {
        return now()->addHours(6);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('attendance.low_alerts_job_failed', [
            'exception' => $exception->getMessage(),
        ]);
    }
}
