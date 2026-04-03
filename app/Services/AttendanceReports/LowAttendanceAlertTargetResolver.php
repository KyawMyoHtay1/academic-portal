<?php

namespace App\Services\AttendanceReports;

use App\Models\Attendance;
use App\Models\LowAttendanceAlertState;
use App\Models\Student;
use App\Services\AttendanceReports\Concerns\BuildsStaffAttendanceReportData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LowAttendanceAlertTargetResolver
{
    use BuildsStaffAttendanceReportData;

    /**
     * @param  array<string, mixed>  $filters
     * @param  Collection<int, array<string, mixed>>  $courses
     * @param  Collection<int, array<string, mixed>>  $subjects
     * @return Collection<int, array<string, mixed>>
     */
    public function buildRows(
        array $filters,
        Collection $courses,
        Collection $subjects,
        ?int $limit = null
    ): Collection {
        $thresholdContext = $this->resolveThresholdContext($filters, $courses, $subjects);
        $effectiveThreshold = $thresholdContext['value'];

        $rows = Student::query()
            ->with('user:id,name,email,preferences')
            ->when(
                $filters['programme'] !== '' && $filters['programme'] !== 'all',
                function (Builder $query) use ($filters) {
                    $query->where('programme', $filters['programme']);
                }
            )
            ->when(
                $filters['intake_year'] !== '' && $filters['intake_year'] !== 'all',
                function (Builder $query) use ($filters) {
                    $query->where('intake_year', $filters['intake_year']);
                }
            )
            ->when(
                $filters['semester'] !== '' && $filters['semester'] !== 'all',
                function (Builder $query) use ($filters) {
                    $query->whereHas('courses', function (Builder $courseQuery) use ($filters) {
                        $courseQuery->where('semester', $filters['semester']);
                    });
                }
            )
            ->get()
            ->map(function (Student $student) use ($filters, $effectiveThreshold, $thresholdContext) {
                $studentAttendance = Attendance::query()
                    ->where('student_id', $student->id);

                $this->applyAttendanceFilters(
                    $studentAttendance,
                    $filters,
                    applyStudentFilters: false,
                    applyIntakeFilter: false
                );

                $total = (clone $studentAttendance)->count();
                $present = (clone $studentAttendance)
                    ->where('status', 'present')
                    ->count();
                $rate = $total > 0 ? round(($present / $total) * 100, 2) : null;

                $preferences = is_array($student->user?->preferences ?? null) ? $student->user->preferences : [];

                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->user?->name ?? $student->full_name,
                    'email' => $student->user?->email ?? $student->email,
                    'programme' => $student->programme,
                    'total' => $total,
                    'present' => $present,
                    'absent' => max($total - $present, 0),
                    'rate' => $rate,
                    'threshold' => $effectiveThreshold,
                    'threshold_label' => $thresholdContext['label'],
                    'deficit' => $rate !== null ? max(round($effectiveThreshold - $rate, 2), 0) : null,
                    'reason' => $rate !== null && $rate < $effectiveThreshold
                        ? sprintf(
                            '%.2f%% below %s threshold',
                            max(round($effectiveThreshold - $rate, 2), 0),
                            $thresholdContext['label']
                        )
                        : null,
                    'notify_attendance' => ($preferences['notify_attendance'] ?? true) !== false,
                    'email_notifications' => ($preferences['email_notifications'] ?? true) === true,
                ];
            })
            ->filter(fn (array $student) => $student['rate'] !== null && $student['rate'] < $effectiveThreshold)
            ->sortBy('rate')
            ->values();

        if ($limit !== null) {
            $rows = $rows->take($limit)->values();
        }

        return $rows;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    public function annotateDispatchability(
        Collection $rows,
        int $cooldownDays,
        bool $bypassCooldown = false
    ): Collection {
        if ($rows->isEmpty()) {
            return collect();
        }

        $statesByStudentId = LowAttendanceAlertState::query()
            ->whereIn('student_id', $rows->pluck('id')->all())
            ->get()
            ->keyBy('student_id');

        $now = now();

        return $rows->map(function (array $row) use ($statesByStudentId, $cooldownDays, $bypassCooldown, $now) {
            $state = $statesByStudentId->get($row['id']);
            $wasBelow = (bool) ($state?->is_below_threshold ?? false);
            $newlyBelow = ! $wasBelow;

            $cooldownOk = true;
            if (! $bypassCooldown && $state?->last_alert_sent_at) {
                $cooldownOk = $state->last_alert_sent_at->lte($now->copy()->subDays($cooldownDays));
            }

            $notificationsEnabled = $row['user_id'] !== null && $row['notify_attendance'];
            $shouldAlert = $notificationsEnabled && ($newlyBelow || $cooldownOk);
            $shouldEmail = $shouldAlert && $row['email_notifications'];

            return array_merge($row, [
                'was_below_threshold' => $wasBelow,
                'cooldown_ok' => $cooldownOk,
                'should_alert' => $shouldAlert,
                'should_email' => $shouldEmail,
            ]);
        })->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, array{student_id: int, rate: float}>
     */
    public function buildJobPayload(Collection $rows): array
    {
        return $rows
            ->map(fn (array $row) => [
                'student_id' => (int) $row['id'],
                'rate' => round((float) $row['rate'], 2),
            ])
            ->values()
            ->all();
    }
}
