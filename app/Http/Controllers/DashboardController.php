<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();

        // Staff/admin view: global stats
        if ($user?->isStaff()) {
            $studentCount = Student::count();
            $courseCount = Course::count();
            $feeTotal = Fee::sum('amount');

            $attendanceTotal = Attendance::count();
            $attendancePresent = Attendance::where('status', 'present')->count();
            $attendanceRate = $attendanceTotal > 0
                ? round(($attendancePresent / $attendanceTotal) * 100, 1)
                : 0;

            // Check alert system status
            $queueConnection = config('queue.default', 'sync');
            $queueConfigured = $queueConnection !== 'sync';
            
            // Check for pending jobs (if using database queue)
            $pendingJobs = 0;
            if ($queueConnection === 'database') {
                try {
                    $pendingJobs = DB::table('jobs')->count();
                } catch (\Exception $e) {
                    // Jobs table might not exist
                }
            }

            // Check scheduler status (we can't detect if it's running, but we can show if configured)
            $schedulerConfigured = true; // We have it configured in bootstrap/app.php
            
            // Determine overall status
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

            // Additional stats for staff dashboard
            $pendingEnrollments = DB::table('course_student')
                ->where('status', 'pending')
                ->count();
            $pendingWithdrawals = DB::table('course_student')
                ->where('status', 'withdrawal_pending')
                ->count();
            $pendingGrades = Grade::where('status', 'pending')->count();
            $pendingPayments = Fee::where('status', 'payment_pending')->count();
            $paidFees = Fee::where('status', 'paid')->sum('amount');
            $pendingFees = Fee::where('status', 'pending')->sum('amount');

            // Chart data: Fee status (doughnut)
            $feeStatusCounts = Fee::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            // Chart data: Enrollments by course (bar) - top 8 courses
            $enrollmentsByCourse = DB::table('course_student')
                ->join('courses', 'courses.id', '=', 'course_student.course_id')
                ->where('course_student.status', 'approved')
                ->select('courses.course_code', DB::raw('count(*) as total'))
                ->groupBy('courses.id', 'courses.course_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
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

            // Chart data: Fees collected by month (line) - last 6 months
            $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
            $feesByMonth = Fee::where('status', 'paid')
                ->whereNotNull('paid_date')
                ->where('paid_date', '>=', $sixMonthsAgo)
                ->selectRaw('YEAR(paid_date) as y, MONTH(paid_date) as m, SUM(amount) as total')
                ->groupBy('y', 'm')
                ->orderBy('y')
                ->orderBy('m')
                ->get();
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

            // Chart data: Grade status (doughnut)
            $gradeStatusCounts = Grade::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            // Extra chart data: Attendance status (doughnut)
            $attendanceStatusCounts = Attendance::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            // Extra chart data: Enrollment status (doughnut)
            $enrollmentStatusCounts = DB::table('course_student')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            return Inertia::render('Dashboard', [
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
            ]);
        }

        // Teacher view: teaching-focused stats
        if ($user?->isTeacher()) {
            $subjectIds = $user->teachingSubjects()->pluck('subjects.id');
            $teachingSubjects = $subjectIds->count();

            // Get unique students from courses that contain the assigned subjects
            $studentsTaught = Student::whereHas('courses.subjects', function ($q) use ($subjectIds) {
                $q->whereIn('subjects.id', $subjectIds);
            })->distinct('students.id')->count();

            $gradesRecorded = Grade::whereIn('subject_id', $subjectIds)->count();

            $attendanceTotal = Attendance::whereIn('subject_id', $subjectIds)->count();
            $attendancePresent = Attendance::whereIn('subject_id', $subjectIds)
                ->where('status', 'present')
                ->count();
            $attendanceRate = $attendanceTotal > 0
                ? round(($attendancePresent / $attendanceTotal) * 100, 1)
                : 0;

            // Additional stats for teacher dashboard
            $pendingGrades = Grade::whereIn('subject_id', $subjectIds)
                ->where('status', 'pending')
                ->count();
            $approvedGrades = Grade::whereIn('subject_id', $subjectIds)
                ->where('status', 'approved')
                ->count();

            // Chart data: Grade status for my subjects (doughnut)
            $myGradeStatusCounts = Grade::whereIn('subject_id', $subjectIds)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            // Chart data: Grades count by subject (bar)
            $gradesBySubject = Grade::whereIn('subject_id', $subjectIds)
                ->join('subjects', 'subjects.id', '=', 'grades.subject_id')
                ->select('subjects.subject_code', DB::raw('count(*) as total'))
                ->groupBy('subjects.id', 'subjects.subject_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
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

            // Chart data: Attendance rate by month (line) - last 6 months for my subjects
            $attendanceByMonth = [];
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subMonths($i)->startOfMonth();
                $end = Carbon::now()->subMonths($i)->endOfMonth();
                $total = Attendance::whereIn('subject_id', $subjectIds)
                    ->whereBetween('date', [$start, $end])
                    ->count();
                $present = Attendance::whereIn('subject_id', $subjectIds)
                    ->whereBetween('date', [$start, $end])
                    ->where('status', 'present')
                    ->count();
                $attendanceByMonth[] = [
                    'label' => $start->format('M Y'),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            }
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

            // Chart data: Assignments by subject (bar) - my subjects
            $assignmentsBySubject = Assignment::whereIn('subject_id', $subjectIds)
                ->join('subjects', 'subjects.id', '=', 'assignments.subject_id')
                ->select('subjects.subject_code', DB::raw('count(*) as total'))
                ->groupBy('subjects.id', 'subjects.subject_code')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
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

            // Extra chart data: Attendance status for my subjects (doughnut)
            $teacherAttendanceStatusCounts = Attendance::whereIn('subject_id', $subjectIds)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
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

            // Extra chart data: Score distribution for approved grades (bar)
            $scoreDistribution = Grade::whereIn('subject_id', $subjectIds)
                ->where('status', Grade::STATUS_APPROVED)
                ->whereNotNull('score')
                ->selectRaw("
                    SUM(CASE WHEN score >= 80 THEN 1 ELSE 0 END) as grade_a,
                    SUM(CASE WHEN score >= 70 AND score < 80 THEN 1 ELSE 0 END) as grade_b,
                    SUM(CASE WHEN score >= 60 AND score < 70 THEN 1 ELSE 0 END) as grade_c,
                    SUM(CASE WHEN score >= 50 AND score < 60 THEN 1 ELSE 0 END) as grade_d,
                    SUM(CASE WHEN score >= 40 AND score < 50 THEN 1 ELSE 0 END) as grade_e,
                    SUM(CASE WHEN score < 40 THEN 1 ELSE 0 END) as grade_f
                ")
                ->first();
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

            return Inertia::render('Dashboard', [
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
            ]);
        }

        // Student view: personal stats
        $student = $user?->student;
        $myCourses = $student?->courses()->count() ?? 0;
        $outstandingFees = $student?->fees()->where('status', 'pending')->sum('amount') ?? 0;
        $myGrades = $student?->grades()->count() ?? 0;

        $attendanceTotal = $student
            ? Attendance::where('student_id', $student->id)->count()
            : 0;
        $attendancePresent = $student
            ? Attendance::where('student_id', $student->id)->where('status', 'present')->count()
            : 0;
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        // Additional stats for student dashboard
        $gpa = $student?->calculateGPA() ?? null;
        $pendingEnrollments = $student
            ? DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('status', 'pending')
                ->count()
            : 0;
        $approvedEnrollments = $student
            ? DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('status', 'approved')
                ->count()
            : 0;

        // Chart data for student dashboard
        $chartsFeeStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#f59e0b', '#10b981'], 'borderWidth' => 0]]];
        $chartsGradesBySubject = ['labels' => [], 'datasets' => [['label' => 'Score', 'data' => [], 'backgroundColor' => 'rgba(139, 92, 246, 0.7)', 'borderColor' => '#8b5cf6', 'borderWidth' => 1]]];
        $chartsAttendanceLine = ['labels' => [], 'datasets' => [['label' => 'Attendance %', 'data' => [], 'borderColor' => '#06b6d4', 'backgroundColor' => 'rgba(6, 182, 212, 0.1)', 'fill' => true, 'tension' => 0.3]]];
        $chartsCourseEnrollment = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#3b82f6', '#f59e0b'], 'borderWidth' => 0]]];
        $chartsAttendanceStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#10b981', '#ef4444'], 'borderWidth' => 0]]];
        $chartsGradeTrendLine = ['labels' => [], 'datasets' => [['label' => 'Avg score', 'data' => [], 'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139, 92, 246, 0.12)', 'fill' => true, 'tension' => 0.3]]];

        if ($student) {
            $feePendingCount = $student->fees()->where('status', 'pending')->count();
            $feePaidCount = $student->fees()->where('status', 'paid')->count();
            $chartsFeeStatus = [
                'labels' => ['Pending', 'Paid'],
                'datasets' => [[
                    'data' => [$feePendingCount, $feePaidCount],
                    'backgroundColor' => ['#f59e0b', '#10b981'],
                    'borderWidth' => 0,
                ]],
            ];

            $gradesBySubject = $student->grades()
                ->where('status', Grade::STATUS_APPROVED)
                ->whereNotNull('score')
                ->join('subjects', 'subjects.id', '=', 'grades.subject_id')
                ->select('subjects.subject_code', DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
                ->groupBy('subjects.id', 'subjects.subject_code')
                ->orderBy('subjects.subject_code')
                ->get();
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

            $attendanceByMonth = [];
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subMonths($i)->startOfMonth();
                $end = Carbon::now()->subMonths($i)->endOfMonth();
                $total = Attendance::where('student_id', $student->id)->whereBetween('date', [$start, $end])->count();
                $present = Attendance::where('student_id', $student->id)->whereBetween('date', [$start, $end])->where('status', 'present')->count();
                $attendanceByMonth[] = [
                    'label' => $start->format('M Y'),
                    'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            }
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

            $gradeTrendRows = Grade::where('student_id', $student->id)
                ->where('status', Grade::STATUS_APPROVED)
                ->whereNotNull('score')
                ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, ROUND(AVG(score), 1) as avg_score')
                ->groupBy('y', 'm')
                ->orderBy('y')
                ->orderBy('m')
                ->get();
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

        return Inertia::render('Dashboard', [
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
        ]);
    }
}
