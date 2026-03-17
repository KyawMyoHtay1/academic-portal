<?php

namespace App\Services\AttendanceReports\Concerns;

use App\Models\Attendance;
use App\Support\AttendanceAlertSettings;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait BuildsStaffAttendanceReportData
{
    /**
     * Apply shared attendance filters to a query.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function applyAttendanceFilters(
        Builder $query,
        array $filters,
        bool $applyStudentFilters = true,
        bool $applyIntakeFilter = true,
        bool $applySemesterFilter = true
    ): void {
        if ($applyStudentFilters && $filters['programme'] !== '' && $filters['programme'] !== 'all') {
            $query->whereHas('student', function (Builder $studentQuery) use ($filters) {
                $studentQuery->where('programme', $filters['programme']);
            });
        }

        if ($applyIntakeFilter && $filters['intake_year'] !== '' && $filters['intake_year'] !== 'all') {
            $query->whereHas('student', function (Builder $studentQuery) use ($filters) {
                $studentQuery->where('intake_year', $filters['intake_year']);
            });
        }

        if ($applySemesterFilter && $filters['semester'] !== '' && $filters['semester'] !== 'all') {
            $query->whereHas('subject.course', function (Builder $courseQuery) use ($filters) {
                $courseQuery->where('semester', $filters['semester']);
            });
        }

        if (($filters['course_id'] ?? null) !== null) {
            $courseId = (int) $filters['course_id'];
            $query->whereHas('subject', function (Builder $subjectQuery) use ($courseId) {
                $subjectQuery->where('course_id', $courseId);
            });
        }

        if (($filters['subject_id'] ?? null) !== null) {
            $query->where('subject_id', (int) $filters['subject_id']);
        }

        if (($filters['date_from'] ?? '') !== '') {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (($filters['date_to'] ?? '') !== '') {
            $query->whereDate('date', '<=', $filters['date_to']);
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $courses
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $subjects
     * @return array{value: float, source: string, label: string}
     */
    protected function resolveThresholdContext(array $filters, \Illuminate\Support\Collection $courses, \Illuminate\Support\Collection $subjects): array
    {
        $normalizeThreshold = static function (mixed $value): ?float {
            if ($value === null || $value === '') {
                return null;
            }

            $numeric = (float) $value;

            if ($numeric < 1 || $numeric > 100) {
                return null;
            }

            return round($numeric, 2);
        };

        $baseThreshold = $normalizeThreshold($filters['threshold'] ?? $this->defaultLowThreshold())
            ?? $this->defaultLowThreshold();
        $courseThreshold = $normalizeThreshold($filters['course_threshold'] ?? null);
        $subjectThreshold = $normalizeThreshold($filters['subject_threshold'] ?? null);
        $hasCourse = ($filters['course_id'] ?? null) !== null;
        $hasSubject = ($filters['subject_id'] ?? null) !== null;
        $selectedCourse = $hasCourse
            ? $courses->firstWhere('id', (int) $filters['course_id'])
            : null;
        $selectedSubject = $hasSubject
            ? $subjects->firstWhere('id', (int) $filters['subject_id'])
            : null;
        $configuredCourseThreshold = is_array($selectedCourse)
            ? $normalizeThreshold($selectedCourse['attendance_threshold'] ?? null)
            : null;
        $configuredSubjectThreshold = is_array($selectedSubject)
            ? $normalizeThreshold($selectedSubject['attendance_threshold'] ?? null)
            : null;

        if ($hasSubject && $subjectThreshold !== null) {
            return [
                'value' => $subjectThreshold,
                'source' => 'subject_override',
                'label' => 'subject override',
            ];
        }

        if ($hasSubject && $configuredSubjectThreshold !== null) {
            return [
                'value' => $configuredSubjectThreshold,
                'source' => 'subject_configured',
                'label' => 'subject configured',
            ];
        }

        if ($hasCourse && $courseThreshold !== null) {
            return [
                'value' => $courseThreshold,
                'source' => 'course_override',
                'label' => 'course override',
            ];
        }

        if ($hasCourse && $configuredCourseThreshold !== null) {
            return [
                'value' => $configuredCourseThreshold,
                'source' => 'course_configured',
                'label' => 'course configured',
            ];
        }

        return [
            'value' => $baseThreshold,
            'source' => 'global',
            'label' => 'global',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, int|float|string>>
     */
    protected function buildWeeklyTrend(array $filters): array
    {
        $firstWeek = CarbonImmutable::now()->startOfWeek()->subWeeks(11);

        $rows = Attendance::query()
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->whereDate('date', '>=', $firstWeek->toDateString())
            ->get(['date', 'status'])
            ->groupBy(function ($attendance) {
                return CarbonImmutable::parse((string) $attendance->date)
                    ->startOfWeek()
                    ->toDateString();
            });

        return collect(range(0, 11))
            ->map(function (int $offset) use ($firstWeek, $rows) {
                $weekStart = $firstWeek->addWeeks($offset);
                $key = $weekStart->toDateString();
                $entries = $rows->get($key, collect());
                $total = $entries->count();
                $present = $entries->where('status', 'present')->count();

                return [
                    'period_start' => $key,
                    'label' => $weekStart->format('M d'),
                    'total' => $total,
                    'present' => $present,
                    'absent' => max($total - $present, 0),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, int|float|string>>
     */
    protected function buildMonthlyTrend(array $filters): array
    {
        $firstMonth = CarbonImmutable::now()->startOfMonth()->subMonths(5);

        $rows = Attendance::query()
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->whereDate('date', '>=', $firstMonth->toDateString())
            ->get(['date', 'status'])
            ->groupBy(function ($attendance) {
                return CarbonImmutable::parse((string) $attendance->date)
                    ->startOfMonth()
                    ->toDateString();
            });

        return collect(range(0, 5))
            ->map(function (int $offset) use ($firstMonth, $rows) {
                $monthStart = $firstMonth->addMonths($offset);
                $key = $monthStart->toDateString();
                $entries = $rows->get($key, collect());
                $total = $entries->count();
                $present = $entries->where('status', 'present')->count();

                return [
                    'period_start' => $key,
                    'label' => $monthStart->format('M Y'),
                    'total' => $total,
                    'present' => $present,
                    'absent' => max($total - $present, 0),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function buildSessionDrilldown(array $filters): array
    {
        $sessionRows = Attendance::query()
            ->select(
                DB::raw('DATE(`date`) as attendance_date'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw("SUM(CASE WHEN `status` = 'present' THEN 1 ELSE 0 END) as present_count")
            )
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->groupBy('attendance_date')
            ->orderByDesc('attendance_date')
            ->limit(60)
            ->get()
            ->map(function ($row) {
                $date = (string) ($row->attendance_date ?? '');
                $total = (int) ($row->total_count ?? 0);
                $present = (int) ($row->present_count ?? 0);

                return [
                    'date' => $date,
                    'total' => $total,
                    'present' => $present,
                    'absent' => max($total - $present, 0),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
                ];
            })
            ->values();

        $selectedDate = '';
        if ($sessionRows->isNotEmpty()) {
            $requestedDate = trim((string) ($filters['session_date'] ?? ''));
            $selectedDate = $sessionRows->pluck('date')->contains($requestedDate)
                ? $requestedDate
                : (string) ($sessionRows->first()['date'] ?? '');
        }

        $records = collect();
        if ($selectedDate !== '') {
            $records = Attendance::query()
                ->with([
                    'student:id,user_id,student_no,programme',
                    'student.user:id,name,email',
                    'subject.course:id,course_code',
                ])
                ->tap(function ($query) use ($filters) {
                    $this->applyAttendanceFilters($query, $filters);
                })
                ->whereDate('date', $selectedDate)
                ->orderBy('status')
                ->orderBy('student_id')
                ->get()
                ->map(function ($attendance) {
                    $student = $attendance->student;
                    $subject = $attendance->subject;
                    $course = $subject?->course;

                    return [
                        'id' => $attendance->id,
                        'student_no' => $student?->student_no ?? 'N/A',
                        'student_name' => $student?->user?->name ?? $student?->full_name ?? 'N/A',
                        'programme' => $student?->programme ?? 'N/A',
                        'subject_code' => $subject?->subject_code ?? 'N/A',
                        'course_code' => $course?->course_code ?? 'N/A',
                        'status' => (string) ($attendance->status ?? 'absent'),
                    ];
                })
                ->values();
        }

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();

        return [
            'sessions' => $sessionRows->all(),
            'selected_date' => $selectedDate !== '' ? $selectedDate : null,
            'summary' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ],
            'records' => $records->all(),
        ];
    }

    protected function defaultLowThreshold(): float
    {
        return AttendanceAlertSettings::lowThreshold();
    }

    protected function defaultCooldownDays(): int
    {
        return AttendanceAlertSettings::cooldownDays();
    }
}

