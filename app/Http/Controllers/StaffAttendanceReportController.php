<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StaffAttendanceReportController extends Controller
{
    /**
     * Display attendance summary report.
     */
    public function index(Request $request): InertiaResponse
    {
        [$filters, $programmes, $intakeYears, $semesters, $courses, $subjects] = $this->resolveReportFilters($request);
        $thresholdContext = $this->resolveThresholdContext($filters);
        $effectiveThreshold = $thresholdContext['value'];

        $attendanceScope = Attendance::query();
        $this->applyAttendanceFilters($attendanceScope, $filters);

        // Overall statistics
        $totalRecords = (clone $attendanceScope)->count();
        $totalPresent = (clone $attendanceScope)->where('status', 'present')->count();
        $totalAbsent = (clone $attendanceScope)->where('status', 'absent')->count();
        $attendanceRate = $totalRecords > 0
            ? round(($totalPresent / $totalRecords) * 100, 2)
            : 0;

        // Attendance by course
        $attendanceByCourse = Course::query()
            ->when(
                $filters['semester'] !== '' && $filters['semester'] !== 'all',
                function (Builder $query) use ($filters) {
                    $query->where('semester', $filters['semester']);
                }
            )
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($filters) {
                    $this->applyAttendanceFilters($query, $filters, applySemesterFilter: false);
                },
                'attendances as present_attendances' => function ($query) use ($filters) {
                    $this->applyAttendanceFilters($query, $filters, applySemesterFilter: false);
                    $query->where('status', 'present');
                },
            ])
            ->orderBy('course_code')
            ->get()
            ->filter(fn ($course) => (int) ($course->total_attendances ?? 0) > 0)
            ->map(function ($course) {
                $rate = $course->total_attendances > 0
                    ? round(($course->present_attendances / $course->total_attendances) * 100, 2)
                    : 0;

                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'total' => $course->total_attendances,
                    'present' => $course->present_attendances,
                    'absent' => $course->total_attendances - $course->present_attendances,
                    'rate' => $rate,
                ];
            });

        // Students with low attendance (below effective threshold)
        $studentsWithLowAttendance = Student::query()
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
            ->map(function ($student) use ($filters, $effectiveThreshold, $thresholdContext) {
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

                return [
                    'id' => $student->id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->full_name,
                    'email' => $student->email,
                    'programme' => $student->programme,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $total - $present,
                    'rate' => $rate,
                    'deficit' => $rate !== null ? max(round($effectiveThreshold - $rate, 2), 0) : null,
                    'reason' => $rate !== null && $rate < $effectiveThreshold
                        ? sprintf(
                            '%.2f%% below %s threshold',
                            max(round($effectiveThreshold - $rate, 2), 0),
                            $thresholdContext['label']
                        )
                        : null,
                ];
            })
            ->filter(function ($student) use ($effectiveThreshold) {
                return $student['rate'] !== null && $student['rate'] < $effectiveThreshold;
            })
            ->sortBy('rate')
            ->values()
            ->take(20); // Top 20 students with lowest attendance

        // Attendance by subject
        $attendanceBySubject = Subject::query()
            ->with('course')
            ->when(
                $filters['semester'] !== '' && $filters['semester'] !== 'all',
                function (Builder $query) use ($filters) {
                    $query->whereHas('course', function (Builder $courseQuery) use ($filters) {
                        $courseQuery->where('semester', $filters['semester']);
                    });
                }
            )
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($filters) {
                    $this->applyAttendanceFilters($query, $filters, applySemesterFilter: false);
                },
                'attendances as present_attendances' => function ($query) use ($filters) {
                    $this->applyAttendanceFilters($query, $filters, applySemesterFilter: false);
                    $query->where('status', 'present');
                },
            ])
            ->orderBy('subject_code')
            ->get()
            ->filter(fn ($subject) => (int) ($subject->total_attendances ?? 0) > 0)
            ->map(function ($subject) {
                $rate = $subject->total_attendances > 0
                    ? round(($subject->present_attendances / $subject->total_attendances) * 100, 2)
                    : 0;

                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course->course_code,
                    'total' => $subject->total_attendances,
                    'present' => $subject->present_attendances,
                    'absent' => $subject->total_attendances - $subject->present_attendances,
                    'rate' => $rate,
                ];
            });

        // Recent attendance records (last 30 days)
        $recentRecords = Attendance::query()
            ->with(['student', 'subject.course'])
            ->where('date', '>=', now()->subDays(30))
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($attendance) {
                $courseCode = $attendance->subject?->course?->course_code ?? 'N/A';

                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date->format('Y-m-d'),
                    'student_no' => $attendance->student->student_no,
                    'student_name' => $attendance->student->full_name,
                    'subject_code' => $attendance->subject?->subject_code ?? 'N/A',
                    'course_code' => $courseCode,
                    'status' => $attendance->status,
                ];
            });

        $trendWeekly = $this->buildWeeklyTrend($filters);
        $trendMonthly = $this->buildMonthlyTrend($filters);
        $sessionDrilldown = $this->buildSessionDrilldown($filters);

        return Inertia::render('Admin/Attendance/Report', [
            'overall' => [
                'total' => $totalRecords,
                'present' => $totalPresent,
                'absent' => $totalAbsent,
                'rate' => $attendanceRate,
            ],
            'byCourse' => $attendanceByCourse,
            'bySubject' => $attendanceBySubject,
            'lowAttendanceStudents' => $studentsWithLowAttendance,
            'recentRecords' => $recentRecords,
            'filters' => $filters,
            'trend' => [
                'weekly' => $trendWeekly,
                'monthly' => $trendMonthly,
            ],
            'sessionDrilldown' => $sessionDrilldown,
            'defaults' => [
                'threshold' => (float) config('attendance_alerts.low_threshold', 75),
                'cooldown_days' => (int) config('attendance_alerts.cooldown_days', 7),
            ],
            'thresholdContext' => $thresholdContext,
            'options' => [
                'programmes' => $programmes,
                'intakeYears' => $intakeYears,
                'semesters' => $semesters,
                'courses' => $courses,
                'subjects' => $subjects,
            ],
        ]);
    }

    /**
     * Export filtered attendance report.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        [$filters] = $this->resolveReportFilters($request);

        $records = Attendance::query()
            ->with(['student:id,student_no,full_name,programme,intake_year', 'subject.course:id,course_code,semester'])
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function ($attendance) {
                $student = $attendance->student;
                $subject = $attendance->subject;
                $course = $subject?->course;

                return [
                    'date' => $attendance->date?->format('Y-m-d'),
                    'student_no' => $student?->student_no ?? 'N/A',
                    'student_name' => $student?->full_name ?? 'N/A',
                    'programme' => $student?->programme ?? 'N/A',
                    'intake_year' => $student?->intake_year ?? 'N/A',
                    'subject_code' => $subject?->subject_code ?? 'N/A',
                    'subject_title' => $subject?->title ?? 'N/A',
                    'course_code' => $course?->course_code ?? 'N/A',
                    'semester' => $course?->semester ?? 'N/A',
                    'status' => $attendance->status,
                ];
            });

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        $timestamp = now()->format('Ymd_His');
        if ($format === 'csv') {
            $filename = "attendance_report_{$timestamp}.csv";

            return response()->streamDownload(function () use ($records): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Date',
                    'Student No',
                    'Student Name',
                    'Programme',
                    'Intake Year',
                    'Subject Code',
                    'Subject Title',
                    'Course Code',
                    'Semester',
                    'Status',
                ]);

                foreach ($records as $row) {
                    fputcsv($handle, [
                        $row['date'],
                        $row['student_no'],
                        $row['student_name'],
                        $row['programme'],
                        $row['intake_year'],
                        $row['subject_code'],
                        $row['subject_title'],
                        $row['course_code'],
                        $row['semester'],
                        $row['status'],
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $pdf = Pdf::loadView('attendance.staff_report', [
            'filters' => $filters,
            'overall' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ],
            'rows' => $records,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("attendance_report_{$timestamp}.pdf");
    }

    /**
     * Apply shared attendance filters to a query.
     *
     * @param  array<string, mixed>  $filters
     */
    private function applyAttendanceFilters(
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
     * Resolve and normalize report filters and options.
     *
     * @return array{
     *   0: array<string, mixed>,
     *   1: \Illuminate\Support\Collection<int, string>,
     *   2: \Illuminate\Support\Collection<int, int>,
     *   3: \Illuminate\Support\Collection<int, string>,
     *   4: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *   5: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    private function resolveReportFilters(Request $request): array
    {
        $defaultThreshold = (float) config('attendance_alerts.low_threshold', 75);
        $defaultCooldown = (int) config('attendance_alerts.cooldown_days', 7);
        $threshold = (float) $request->input('threshold', $defaultThreshold);
        if ($threshold < 1 || $threshold > 100) {
            $threshold = $defaultThreshold;
        }

        $cooldownDays = (int) $request->input('cooldown_days', $defaultCooldown);
        if ($cooldownDays < 0 || $cooldownDays > 90) {
            $cooldownDays = $defaultCooldown;
        }

        $dateFrom = trim((string) $request->input('date_from', ''));
        $dateTo = trim((string) $request->input('date_to', ''));
        $sessionDate = trim((string) $request->input('session_date', ''));
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sessionDate)) {
            $sessionDate = '';
        }
        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $trendMode = trim((string) $request->input('trend_mode', 'weekly'));
        if (! in_array($trendMode, ['weekly', 'monthly'], true)) {
            $trendMode = 'weekly';
        }

        $courseId = (int) $request->input('course_id', 0);
        $subjectId = (int) $request->input('subject_id', 0);
        $courseId = $courseId > 0 ? $courseId : null;
        $subjectId = $subjectId > 0 ? $subjectId : null;

        $courseThresholdInput = trim((string) $request->input('course_threshold', ''));
        $subjectThresholdInput = trim((string) $request->input('subject_threshold', ''));
        $courseThreshold = is_numeric($courseThresholdInput) ? (float) $courseThresholdInput : null;
        if ($courseThreshold !== null && ($courseThreshold < 1 || $courseThreshold > 100)) {
            $courseThreshold = null;
        }
        $subjectThreshold = is_numeric($subjectThresholdInput) ? (float) $subjectThresholdInput : null;
        if ($subjectThreshold !== null && ($subjectThreshold < 1 || $subjectThreshold > 100)) {
            $subjectThreshold = null;
        }

        $filters = [
            'programme' => trim((string) $request->input('programme', 'all')),
            'intake_year' => trim((string) $request->input('intake_year', 'all')),
            'semester' => trim((string) $request->input('semester', 'all')),
            'course_id' => $courseId,
            'subject_id' => $subjectId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'session_date' => $sessionDate,
            'threshold' => round($threshold, 2),
            'course_threshold' => $courseThreshold !== null ? round($courseThreshold, 2) : null,
            'subject_threshold' => $subjectThreshold !== null ? round($subjectThreshold, 2) : null,
            'cooldown_days' => $cooldownDays,
            'trend_mode' => $trendMode,
        ];

        $programmes = Student::query()
            ->whereNotNull('programme')
            ->where('programme', '!=', '')
            ->distinct()
            ->orderBy('programme')
            ->pluck('programme')
            ->values();

        $intakeYears = Student::query()
            ->whereNotNull('intake_year')
            ->distinct()
            ->orderByDesc('intake_year')
            ->pluck('intake_year')
            ->values();

        $semesters = Course::query()
            ->whereNotNull('semester')
            ->where('semester', '!=', '')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->values();

        $courses = Course::query()
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'title'])
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                ];
            })
            ->values();

        $subjects = Subject::query()
            ->with('course:id,course_code')
            ->orderBy('subject_code')
            ->get(['id', 'course_id', 'subject_code', 'title'])
            ->map(function (Subject $subject) {
                return [
                    'id' => $subject->id,
                    'course_id' => $subject->course_id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course?->course_code,
                ];
            })
            ->values();

        if ($filters['programme'] !== 'all' && ! $programmes->contains($filters['programme'])) {
            $filters['programme'] = 'all';
        }
        if ($filters['intake_year'] !== 'all' && ! $intakeYears->map(fn ($year) => (string) $year)->contains($filters['intake_year'])) {
            $filters['intake_year'] = 'all';
        }
        if ($filters['semester'] !== 'all' && ! $semesters->contains($filters['semester'])) {
            $filters['semester'] = 'all';
        }

        if ($filters['course_id'] !== null && ! $courses->pluck('id')->contains($filters['course_id'])) {
            $filters['course_id'] = null;
        }

        if ($filters['subject_id'] !== null && ! $subjects->pluck('id')->contains($filters['subject_id'])) {
            $filters['subject_id'] = null;
        }

        if ($filters['subject_id'] !== null && $filters['course_id'] !== null) {
            $selectedSubject = $subjects->firstWhere('id', $filters['subject_id']);
            if (! $selectedSubject || (int) $selectedSubject['course_id'] !== (int) $filters['course_id']) {
                $filters['subject_id'] = null;
            }
        }

        if ($filters['subject_id'] !== null && $filters['course_id'] === null) {
            $selectedSubject = $subjects->firstWhere('id', $filters['subject_id']);
            $filters['course_id'] = $selectedSubject ? (int) $selectedSubject['course_id'] : null;
        }

        return [$filters, $programmes, $intakeYears, $semesters, $courses, $subjects];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{value: float, source: string, label: string}
     */
    private function resolveThresholdContext(array $filters): array
    {
        $baseThreshold = (float) ($filters['threshold'] ?? config('attendance_alerts.low_threshold', 75));
        $courseThreshold = $filters['course_threshold'] !== null
            ? (float) $filters['course_threshold']
            : null;
        $subjectThreshold = $filters['subject_threshold'] !== null
            ? (float) $filters['subject_threshold']
            : null;
        $hasCourse = ($filters['course_id'] ?? null) !== null;
        $hasSubject = ($filters['subject_id'] ?? null) !== null;

        if ($hasSubject && $subjectThreshold !== null) {
            return [
                'value' => $subjectThreshold,
                'source' => 'subject',
                'label' => 'subject',
            ];
        }

        if ($hasCourse && $courseThreshold !== null) {
            return [
                'value' => $courseThreshold,
                'source' => 'course',
                'label' => 'course',
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
    private function buildWeeklyTrend(array $filters): array
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
    private function buildMonthlyTrend(array $filters): array
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
    private function buildSessionDrilldown(array $filters): array
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
                ->with(['student:id,student_no,full_name,programme', 'subject.course:id,course_code'])
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
                        'student_name' => $student?->full_name ?? 'N/A',
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
}
