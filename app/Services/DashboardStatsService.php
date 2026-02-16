<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Build dashboard payload for the current role.
     *
     * @return array<string, mixed>
     */
    public function build(?User $user): array
    {
        $cacheTtl = now()->addMinutes(2);

        if ($user?->isStaff()) {
            return $this->buildStaffPayload($cacheTtl);
        }

        if ($user?->isTeacher()) {
            return $this->buildTeacherPayload($user, $cacheTtl);
        }

        return $this->buildStudentPayload($user, $cacheTtl);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStaffPayload(\DateTimeInterface $cacheTtl): array
    {
        $studentCount = Cache::remember('dashboard:staff:student_count', $cacheTtl, fn () => Student::count());
        $courseCount = Cache::remember('dashboard:staff:course_count', $cacheTtl, fn () => Course::count());
        $feeTotal = Cache::remember('dashboard:staff:fee_total', $cacheTtl, fn () => Fee::sum('amount'));

        $attendanceTotal = Cache::remember('dashboard:staff:attendance_total', $cacheTtl, fn () => Attendance::count());
        $attendancePresent = Cache::remember('dashboard:staff:attendance_present', $cacheTtl, fn () => Attendance::where('status', 'present')->count());
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        $queueConnection = config('queue.default', 'sync');
        $queueConfigured = $queueConnection !== 'sync';

        $pendingJobs = 0;
        if ($queueConnection === 'database') {
            try {
                $pendingJobs = DB::table('jobs')->count();
            } catch (\Exception $e) {
                // Jobs table might not exist.
            }
        }

        $schedulerConfigured = true;
        $alertSystemStatus = [
            'queueConfigured' => $queueConfigured,
            'queueConnection' => $queueConnection,
            'pendingJobs' => $pendingJobs,
            'schedulerConfigured' => $schedulerConfigured,
            'status' => $queueConfigured && $schedulerConfigured ? 'ready' : 'warning',
            'message' => $queueConfigured
                ? ($pendingJobs > 10 ? 'Queue worker may need attention (many pending jobs)' : 'System ready for automatic alerts')
                : 'Queue is set to sync mode - configure QUEUE_CONNECTION for automatic alerts',
        ];

        $pendingEnrollments = Cache::remember('dashboard:staff:pending_enrollments', $cacheTtl, function () {
            return DB::table('course_student')
                ->where('status', 'pending')
                ->count();
        });
        $pendingWithdrawals = Cache::remember('dashboard:staff:pending_withdrawals', $cacheTtl, function () {
            return DB::table('course_student')
                ->where('status', 'withdrawal_pending')
                ->count();
        });
        $pendingGrades = Cache::remember('dashboard:staff:pending_grades', $cacheTtl, fn () => Grade::where('status', 'pending')->count());
        $pendingPayments = Cache::remember('dashboard:staff:pending_payments', $cacheTtl, fn () => Fee::where('status', 'payment_pending')->count());
        $paidFees = Cache::remember('dashboard:staff:paid_fees_total', $cacheTtl, fn () => Fee::where('status', 'paid')->sum('amount'));
        $pendingFees = Cache::remember('dashboard:staff:pending_fees_total', $cacheTtl, fn () => Fee::where('status', 'pending')->sum('amount'));

        $feeStatusCounts = Cache::remember('dashboard:staff:fee_status_counts', $cacheTtl, function () {
            return Fee::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsFeeStatus = [
            'labels' => ['Pending', 'Payment Pending', 'Paid'],
            'datasets' => [[
                'data' => [
                    $feeStatusCounts['pending'] ?? 0,
                    $feeStatusCounts['payment_pending'] ?? 0,
                    $feeStatusCounts['paid'] ?? 0,
                ],
                'backgroundColor' => ['#f59e0b', '#10b981', '#3b82f6'],
                'borderWidth' => 0,
            ]],
        ];

        $enrollmentsByCourse = Cache::remember('dashboard:staff:enrollments_by_course', $cacheTtl, function () {
            return DB::table('course_student')
                ->join('courses', 'courses.id', '=', 'course_student.course_id')
                ->where('course_student.status', 'approved')
                ->select('courses.course_code', DB::raw('count(*) as total'))
                ->groupBy('courses.id', 'courses.course_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        });
        $chartsEnrollmentsByCourse = [
            'labels' => $enrollmentsByCourse->pluck('course_code')->toArray(),
            'datasets' => [[
                'label' => 'Enrolled students',
                'data' => $enrollmentsByCourse->pluck('total')->toArray(),
                'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                'borderColor' => '#3b82f6',
                'borderWidth' => 1,
            ]],
        ];

        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $feesByMonth = Cache::remember('dashboard:staff:fees_by_month', $cacheTtl, function () use ($sixMonthsAgo) {
            return Fee::where('status', 'paid')
                ->whereNotNull('paid_date')
                ->where('paid_date', '>=', $sixMonthsAgo)
                ->selectRaw('YEAR(paid_date) as y, MONTH(paid_date) as m, SUM(amount) as total')
                ->groupBy('y', 'm')
                ->orderBy('y')
                ->orderBy('m')
                ->get();
        });
        $monthLabels = [];
        $monthTotals = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $monthLabels[] = $d->format('M Y');
            $found = $feesByMonth->first(fn ($r) => (int) $r->y === (int) $d->year && (int) $r->m === (int) $d->month);
            $monthTotals[] = $found ? (float) $found->total : 0;
        }
        $chartsFeesCollectedLine = [
            'labels' => $monthLabels,
            'datasets' => [[
                'label' => 'Fees collected (GBP)',
                'data' => $monthTotals,
                'borderColor' => '#10b981',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
        ];

        $gradeStatusCounts = Cache::remember('dashboard:staff:grade_status_counts', $cacheTtl, function () {
            return Grade::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsGradeStatus = [
            'labels' => ['Pending', 'Approved', 'Rejected'],
            'datasets' => [[
                'data' => [
                    $gradeStatusCounts['pending'] ?? 0,
                    $gradeStatusCounts['approved'] ?? 0,
                    $gradeStatusCounts['rejected'] ?? 0,
                ],
                'backgroundColor' => ['#8b5cf6', '#22c55e', '#ef4444'],
                'borderWidth' => 0,
            ]],
        ];

        $attendanceStatusCounts = Cache::remember('dashboard:staff:attendance_status_counts', $cacheTtl, function () {
            return Attendance::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsAttendanceStatus = [
            'labels' => ['Present', 'Absent'],
            'datasets' => [[
                'data' => [
                    $attendanceStatusCounts['present'] ?? 0,
                    $attendanceStatusCounts['absent'] ?? 0,
                ],
                'backgroundColor' => ['#10b981', '#ef4444'],
                'borderWidth' => 0,
            ]],
        ];

        $enrollmentStatusCounts = Cache::remember('dashboard:staff:enrollment_status_counts', $cacheTtl, function () {
            return DB::table('course_student')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsEnrollmentStatus = [
            'labels' => ['Pending', 'Approved', 'Rejected', 'Withdrawal Pending'],
            'datasets' => [[
                'data' => [
                    $enrollmentStatusCounts['pending'] ?? 0,
                    $enrollmentStatusCounts['approved'] ?? 0,
                    $enrollmentStatusCounts['rejected'] ?? 0,
                    $enrollmentStatusCounts['withdrawal_pending'] ?? 0,
                ],
                'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                'borderWidth' => 0,
            ]],
        ];

        return [
            'role' => 'staff',
            'stats' => [
                'students' => $studentCount,
                'courses' => $courseCount,
                'feeTotal' => $feeTotal,
                'attendanceRate' => $attendanceRate,
                'pendingEnrollments' => $pendingEnrollments,
                'pendingWithdrawals' => $pendingWithdrawals,
                'pendingGrades' => $pendingGrades,
                'pendingPayments' => $pendingPayments,
                'paidFees' => $paidFees,
                'pendingFees' => $pendingFees,
            ],
            'charts' => [
                'feeStatus' => $chartsFeeStatus,
                'enrollmentsByCourse' => $chartsEnrollmentsByCourse,
                'feesCollectedLine' => $chartsFeesCollectedLine,
                'gradeStatus' => $chartsGradeStatus,
                'attendanceStatus' => $chartsAttendanceStatus,
                'enrollmentStatus' => $chartsEnrollmentStatus,
            ],
            'alertSystemStatus' => $alertSystemStatus,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTeacherPayload(User $user, \DateTimeInterface $cacheTtl): array
    {
        $subjectIds = collect(Cache::remember("dashboard:teacher:{$user->id}:subject_ids", $cacheTtl, function () use ($user) {
            return $user->teachingSubjects()->pluck('subjects.id')->toArray();
        }));
        $teachingSubjects = $subjectIds->count();
        $subjectIdArray = $subjectIds->toArray();

        $studentsTaught = Cache::remember("dashboard:teacher:{$user->id}:students_taught", $cacheTtl, function () use ($subjectIdArray) {
            return Student::whereHas('courses.subjects', function ($q) use ($subjectIdArray) {
                $q->whereIn('subjects.id', $subjectIdArray);
            })->distinct('students.id')->count();
        });
        $gradesRecorded = Cache::remember("dashboard:teacher:{$user->id}:grades_recorded", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)->count();
        });
        $attendanceTotal = Cache::remember("dashboard:teacher:{$user->id}:attendance_total", $cacheTtl, function () use ($subjectIdArray) {
            return Attendance::whereIn('subject_id', $subjectIdArray)->count();
        });
        $attendancePresent = Cache::remember("dashboard:teacher:{$user->id}:attendance_present", $cacheTtl, function () use ($subjectIdArray) {
            return Attendance::whereIn('subject_id', $subjectIdArray)
                ->where('status', 'present')
                ->count();
        });
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        $pendingGrades = Cache::remember("dashboard:teacher:{$user->id}:pending_grades", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->where('status', 'pending')
                ->count();
        });
        $approvedGrades = Cache::remember("dashboard:teacher:{$user->id}:approved_grades", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->where('status', 'approved')
                ->count();
        });

        $myGradeStatusCounts = Cache::remember("dashboard:teacher:{$user->id}:grade_status_counts", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsGradeStatus = [
            'labels' => ['Pending', 'Approved', 'Rejected'],
            'datasets' => [[
                'data' => [
                    $myGradeStatusCounts['pending'] ?? 0,
                    $myGradeStatusCounts['approved'] ?? 0,
                    $myGradeStatusCounts['rejected'] ?? 0,
                ],
                'backgroundColor' => ['#8b5cf6', '#22c55e', '#ef4444'],
                'borderWidth' => 0,
            ]],
        ];

        $gradesBySubject = Cache::remember("dashboard:teacher:{$user->id}:grades_by_subject", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->join('subjects', 'subjects.id', '=', 'grades.subject_id')
                ->select('subjects.subject_code', DB::raw('count(*) as total'))
                ->groupBy('subjects.id', 'subjects.subject_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        });
        $chartsGradesBySubject = [
            'labels' => $gradesBySubject->pluck('subject_code')->toArray(),
            'datasets' => [[
                'label' => 'Grades recorded',
                'data' => $gradesBySubject->pluck('total')->toArray(),
                'backgroundColor' => 'rgba(139, 92, 246, 0.7)',
                'borderColor' => '#8b5cf6',
                'borderWidth' => 1,
            ]],
        ];

        $attendanceByMonth = Cache::remember("dashboard:teacher:{$user->id}:attendance_by_month", $cacheTtl, function () use ($subjectIdArray) {
            $rows = [];
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subMonths($i)->startOfMonth();
                $end = Carbon::now()->subMonths($i)->endOfMonth();
                $total = Attendance::whereIn('subject_id', $subjectIdArray)
                    ->whereBetween('date', [$start, $end])
                    ->count();
                $present = Attendance::whereIn('subject_id', $subjectIdArray)
                    ->whereBetween('date', [$start, $end])
                    ->where('status', 'present')
                    ->count();
                $rows[] = [
                    'label' => $start->format('M Y'),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            }

            return $rows;
        });
        $chartsAttendanceLine = [
            'labels' => array_column($attendanceByMonth, 'label'),
            'datasets' => [[
                'label' => 'Attendance %',
                'data' => array_column($attendanceByMonth, 'rate'),
                'borderColor' => '#06b6d4',
                'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
        ];

        $assignmentsBySubject = Cache::remember("dashboard:teacher:{$user->id}:assignments_by_subject", $cacheTtl, function () use ($subjectIdArray) {
            return Assignment::whereIn('subject_id', $subjectIdArray)
                ->join('subjects', 'subjects.id', '=', 'assignments.subject_id')
                ->select('subjects.subject_code', DB::raw('count(*) as total'))
                ->groupBy('subjects.id', 'subjects.subject_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        });
        $chartsAssignmentsBySubject = [
            'labels' => $assignmentsBySubject->pluck('subject_code')->toArray(),
            'datasets' => [[
                'label' => 'Assignments',
                'data' => $assignmentsBySubject->pluck('total')->toArray(),
                'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                'borderColor' => '#10b981',
                'borderWidth' => 1,
            ]],
        ];

        $teacherAttendanceStatusCounts = Cache::remember("dashboard:teacher:{$user->id}:attendance_status_counts", $cacheTtl, function () use ($subjectIdArray) {
            return Attendance::whereIn('subject_id', $subjectIdArray)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsAttendanceStatus = [
            'labels' => ['Present', 'Absent'],
            'datasets' => [[
                'data' => [
                    $teacherAttendanceStatusCounts['present'] ?? 0,
                    $teacherAttendanceStatusCounts['absent'] ?? 0,
                ],
                'backgroundColor' => ['#10b981', '#ef4444'],
                'borderWidth' => 0,
            ]],
        ];

        $scoreDistribution = Cache::remember("dashboard:teacher:{$user->id}:score_distribution", $cacheTtl, function () use ($subjectIdArray) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->where('status', Grade::STATUS_APPROVED)
                ->whereNotNull('score')
                ->selectRaw('
                    SUM(CASE WHEN score >= 80 THEN 1 ELSE 0 END) as grade_a,
                    SUM(CASE WHEN score >= 70 AND score < 80 THEN 1 ELSE 0 END) as grade_b,
                    SUM(CASE WHEN score >= 60 AND score < 70 THEN 1 ELSE 0 END) as grade_c,
                    SUM(CASE WHEN score >= 50 AND score < 60 THEN 1 ELSE 0 END) as grade_d,
                    SUM(CASE WHEN score >= 40 AND score < 50 THEN 1 ELSE 0 END) as grade_e,
                    SUM(CASE WHEN score < 40 THEN 1 ELSE 0 END) as grade_f
                ')
                ->first();
        });
        $chartsScoreDistribution = [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'E (40-49)', 'F (<40)'],
            'datasets' => [[
                'label' => 'Grade entries',
                'data' => [
                    (int) ($scoreDistribution?->grade_a ?? 0),
                    (int) ($scoreDistribution?->grade_b ?? 0),
                    (int) ($scoreDistribution?->grade_c ?? 0),
                    (int) ($scoreDistribution?->grade_d ?? 0),
                    (int) ($scoreDistribution?->grade_e ?? 0),
                    (int) ($scoreDistribution?->grade_f ?? 0),
                ],
                'backgroundColor' => 'rgba(79, 70, 229, 0.7)',
                'borderColor' => '#4f46e5',
                'borderWidth' => 1,
            ]],
        ];

        return [
            'role' => 'teacher',
            'stats' => [
                'teachingSubjects' => $teachingSubjects,
                'studentsTaught' => $studentsTaught,
                'gradesRecorded' => $gradesRecorded,
                'attendanceRate' => $attendanceRate,
                'pendingGrades' => $pendingGrades,
                'approvedGrades' => $approvedGrades,
            ],
            'charts' => [
                'gradeStatus' => $chartsGradeStatus,
                'gradesBySubject' => $chartsGradesBySubject,
                'attendanceLine' => $chartsAttendanceLine,
                'assignmentsBySubject' => $chartsAssignmentsBySubject,
                'attendanceStatus' => $chartsAttendanceStatus,
                'scoreDistribution' => $chartsScoreDistribution,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStudentPayload(?User $user, \DateTimeInterface $cacheTtl): array
    {
        $student = $user?->student;
        $studentCacheKeyPrefix = "dashboard:student:{$user?->id}";

        $myCourses = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:my_courses", $cacheTtl, fn () => $student->courses()->count())
            : 0;
        $outstandingFees = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:outstanding_fees", $cacheTtl, fn () => $student->fees()->where('status', 'pending')->sum('amount'))
            : 0;
        $myGrades = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:my_grades", $cacheTtl, fn () => $student->grades()->count())
            : 0;

        $attendanceTotal = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:attendance_total", $cacheTtl, fn () => Attendance::where('student_id', $student->id)->count())
            : 0;
        $attendancePresent = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:attendance_present", $cacheTtl, fn () => Attendance::where('student_id', $student->id)->where('status', 'present')->count())
            : 0;
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        $gpa = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:gpa", $cacheTtl, fn () => $student->calculateGPA())
            : null;
        $pendingEnrollments = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:pending_enrollments", $cacheTtl, function () use ($student) {
                return DB::table('course_student')
                    ->where('student_id', $student->id)
                    ->where('status', 'pending')
                    ->count();
            })
            : 0;
        $approvedEnrollments = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:approved_enrollments", $cacheTtl, function () use ($student) {
                return DB::table('course_student')
                    ->where('student_id', $student->id)
                    ->where('status', 'approved')
                    ->count();
            })
            : 0;

        $chartsFeeStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#f59e0b', '#10b981'], 'borderWidth' => 0]]];
        $chartsGradesBySubject = ['labels' => [], 'datasets' => [['label' => 'Score', 'data' => [], 'backgroundColor' => 'rgba(139, 92, 246, 0.7)', 'borderColor' => '#8b5cf6', 'borderWidth' => 1]]];
        $chartsAttendanceLine = ['labels' => [], 'datasets' => [['label' => 'Attendance %', 'data' => [], 'borderColor' => '#06b6d4', 'backgroundColor' => 'rgba(6, 182, 212, 0.1)', 'fill' => true, 'tension' => 0.3]]];
        $chartsCourseEnrollment = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#3b82f6', '#f59e0b'], 'borderWidth' => 0]]];
        $chartsAttendanceStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#10b981', '#ef4444'], 'borderWidth' => 0]]];
        $chartsGradeTrendLine = ['labels' => [], 'datasets' => [['label' => 'Avg score', 'data' => [], 'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139, 92, 246, 0.12)', 'fill' => true, 'tension' => 0.3]]];

        if ($student) {
            $feePendingCount = Cache::remember("{$studentCacheKeyPrefix}:fee_pending_count", $cacheTtl, fn () => $student->fees()->where('status', 'pending')->count());
            $feePaidCount = Cache::remember("{$studentCacheKeyPrefix}:fee_paid_count", $cacheTtl, fn () => $student->fees()->where('status', 'paid')->count());
            $chartsFeeStatus = [
                'labels' => ['Pending', 'Paid'],
                'datasets' => [[
                    'data' => [$feePendingCount, $feePaidCount],
                    'backgroundColor' => ['#f59e0b', '#10b981'],
                    'borderWidth' => 0,
                ]],
            ];

            $gradesBySubject = Cache::remember("{$studentCacheKeyPrefix}:grades_by_subject", $cacheTtl, function () use ($student) {
                return $student->grades()
                    ->where('status', Grade::STATUS_APPROVED)
                    ->whereNotNull('score')
                    ->join('subjects', 'subjects.id', '=', 'grades.subject_id')
                    ->select('subjects.subject_code', DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
                    ->groupBy('subjects.id', 'subjects.subject_code')
                    ->orderBy('subjects.subject_code')
                    ->get();
            });
            $chartsGradesBySubject = [
                'labels' => $gradesBySubject->pluck('subject_code')->toArray(),
                'datasets' => [[
                    'label' => 'Avg score',
                    'data' => $gradesBySubject->pluck('avg_score')->map(fn ($s) => (float) $s)->toArray(),
                    'backgroundColor' => 'rgba(139, 92, 246, 0.7)',
                    'borderColor' => '#8b5cf6',
                    'borderWidth' => 1,
                ]],
            ];

            $attendanceByMonth = Cache::remember("{$studentCacheKeyPrefix}:attendance_by_month", $cacheTtl, function () use ($student) {
                $rows = [];
                for ($i = 5; $i >= 0; $i--) {
                    $start = Carbon::now()->subMonths($i)->startOfMonth();
                    $end = Carbon::now()->subMonths($i)->endOfMonth();
                    $total = Attendance::where('student_id', $student->id)->whereBetween('date', [$start, $end])->count();
                    $present = Attendance::where('student_id', $student->id)->whereBetween('date', [$start, $end])->where('status', 'present')->count();
                    $rows[] = [
                        'label' => $start->format('M Y'),
                        'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                    ];
                }

                return $rows;
            });
            $chartsAttendanceLine = [
                'labels' => array_column($attendanceByMonth, 'label'),
                'datasets' => [[
                    'label' => 'Attendance %',
                    'data' => array_column($attendanceByMonth, 'rate'),
                    'borderColor' => '#06b6d4',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ]],
            ];

            $chartsCourseEnrollment = [
                'labels' => ['Enrolled', 'Pending'],
                'datasets' => [[
                    'data' => [$approvedEnrollments, $pendingEnrollments],
                    'backgroundColor' => ['#3b82f6', '#f59e0b'],
                    'borderWidth' => 0,
                ]],
            ];

            $chartsAttendanceStatus = [
                'labels' => ['Present', 'Absent'],
                'datasets' => [[
                    'data' => [$attendancePresent, max($attendanceTotal - $attendancePresent, 0)],
                    'backgroundColor' => ['#10b981', '#ef4444'],
                    'borderWidth' => 0,
                ]],
            ];

            $gradeTrendRows = Cache::remember("{$studentCacheKeyPrefix}:grade_trend_rows", $cacheTtl, function () use ($student) {
                return Grade::where('student_id', $student->id)
                    ->where('status', Grade::STATUS_APPROVED)
                    ->whereNotNull('score')
                    ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                    ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, ROUND(AVG(score), 1) as avg_score')
                    ->groupBy('y', 'm')
                    ->orderBy('y')
                    ->orderBy('m')
                    ->get();
            });
            $gradeTrendLabels = [];
            $gradeTrendValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = Carbon::now()->subMonths($i);
                $gradeTrendLabels[] = $d->format('M Y');
                $found = $gradeTrendRows->first(fn ($r) => (int) $r->y === (int) $d->year && (int) $r->m === (int) $d->month);
                $gradeTrendValues[] = $found ? (float) $found->avg_score : 0;
            }
            $chartsGradeTrendLine = [
                'labels' => $gradeTrendLabels,
                'datasets' => [[
                    'label' => 'Avg score',
                    'data' => $gradeTrendValues,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ]],
            ];
        }

        return [
            'role' => 'student',
            'stats' => [
                'myCourses' => $myCourses,
                'outstandingFees' => $outstandingFees,
                'myGrades' => $myGrades,
                'attendanceRate' => $attendanceRate,
                'gpa' => $gpa,
                'pendingEnrollments' => $pendingEnrollments,
                'approvedEnrollments' => $approvedEnrollments,
            ],
            'charts' => [
                'feeStatus' => $chartsFeeStatus,
                'gradesBySubject' => $chartsGradesBySubject,
                'attendanceLine' => $chartsAttendanceLine,
                'courseEnrollment' => $chartsCourseEnrollment,
                'attendanceStatus' => $chartsAttendanceStatus,
                'gradeTrendLine' => $chartsGradeTrendLine,
            ],
        ];
    }
}
