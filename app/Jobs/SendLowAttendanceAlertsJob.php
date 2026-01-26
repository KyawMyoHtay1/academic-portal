<?php

namespace App\Jobs;

use App\Models\LowAttendanceAlertState;
use App\Models\Student;
use App\Notifications\LowAttendanceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendLowAttendanceAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $threshold = (float) config('attendance_alerts.low_threshold', 75);
        $cooldownDays = (int) config('attendance_alerts.cooldown_days', 7);
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
                    $newlyBelow = $isBelow && !$wasBelow;

                    $cooldownOk = true;
                    if ($state?->last_alert_sent_at) {
                        $cooldownOk = $state->last_alert_sent_at->lte($now->copy()->subDays($cooldownDays));
                    }

                    $lastAlertSentAt = $state?->last_alert_sent_at;
                    if ($newlyBelow && $cooldownOk && $student && $student->user) {
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
}

