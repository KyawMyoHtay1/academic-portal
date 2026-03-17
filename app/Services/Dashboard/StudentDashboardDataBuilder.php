<?php

namespace App\Services\Dashboard;

use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\User;
use App\Services\Dashboard\Concerns\BuildsDashboardTrendInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentDashboardDataBuilder
{
    use BuildsDashboardTrendInsight;

    /**
     * @return array<string, mixed>
     */
    public function build(?User $user, \DateTimeInterface $cacheTtl): array
    {
        $student = $user?->student;
        $studentCacheKeyPrefix = "dashboard:student:{$user?->id}";

        $myCourses = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:my_courses", $cacheTtl, fn () => $student->courses()->count())
            : 0;
        $outstandingFees = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:outstanding_fees", $cacheTtl, fn () => $student->fees()->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])->sum('amount'))
            : 0;
        $outstandingBalance = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:outstanding_balance", $cacheTtl, fn () => $student->fees()->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])->sum('amount'))
            : 0;
        $paidFees = $student
            ? Cache::remember("{$studentCacheKeyPrefix}:paid_fees", $cacheTtl, fn () => $student->fees()->where('status', Fee::STATUS_PAID)->sum('amount'))
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

        $chartsFeeStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'], 'borderWidth' => 0]]];
        $chartsGradesBySubject = ['labels' => [], 'datasets' => [['label' => 'Score', 'data' => [], 'backgroundColor' => 'rgba(139, 92, 246, 0.7)', 'borderColor' => '#8b5cf6', 'borderWidth' => 1]]];
        $chartsAttendanceLine = ['labels' => [], 'datasets' => [['label' => 'Attendance %', 'data' => [], 'borderColor' => '#06b6d4', 'backgroundColor' => 'rgba(6, 182, 212, 0.1)', 'fill' => true, 'tension' => 0.3]]];
        $chartsCourseEnrollment = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#3b82f6', '#f59e0b'], 'borderWidth' => 0]]];
        $chartsAttendanceStatus = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => ['#10b981', '#ef4444'], 'borderWidth' => 0]]];
        $chartsGradeTrendLine = ['labels' => [], 'datasets' => [['label' => 'Avg score', 'data' => [], 'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139, 92, 246, 0.12)', 'fill' => true, 'tension' => 0.3]]];
        $studentInsights = [
            'feeProgress' => [
                'paidAmount' => (float) $paidFees,
                'outstandingAmount' => (float) $outstandingBalance,
                'totalAmount' => (float) ($paidFees + $outstandingBalance),
                'paidPercent' => ($paidFees + $outstandingBalance) > 0
                    ? round(((float) $paidFees / (float) ($paidFees + $outstandingBalance)) * 100, 1)
                    : 0,
            ],
            'attendanceTrend' => [
                ...$this->buildTrendInsight(0, 0),
                'currentLabel' => 'This month',
                'previousLabel' => 'Last month',
            ],
            'riskSubjects' => [],
        ];

        if ($student) {
            $feePendingCount = Cache::remember("{$studentCacheKeyPrefix}:fee_pending_count", $cacheTtl, fn () => $student->fees()->where('status', Fee::STATUS_PENDING)->count());
            $feePaymentPendingCount = Cache::remember("{$studentCacheKeyPrefix}:fee_payment_pending_count", $cacheTtl, fn () => $student->fees()->where('status', Fee::STATUS_PAYMENT_PENDING)->count());
            $feeFailedCount = Cache::remember("{$studentCacheKeyPrefix}:fee_failed_count", $cacheTtl, fn () => $student->fees()->where('status', Fee::STATUS_FAILED)->count());
            $feePaidCount = Cache::remember("{$studentCacheKeyPrefix}:fee_paid_count", $cacheTtl, fn () => $student->fees()->where('status', 'paid')->count());
            $chartsFeeStatus = [
                'labels' => ['Pending', 'Payment Pending', 'Failed', 'Paid'],
                'datasets' => [[
                    'data' => [$feePendingCount, $feePaymentPendingCount, $feeFailedCount, $feePaidCount],
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
                    'borderWidth' => 0,
                ]],
            ];

            $gradesBySubject = Cache::remember("{$studentCacheKeyPrefix}:grades_by_subject", $cacheTtl, function () use ($student) {
                return $student->grades()
                    ->where('status', Grade::STATUS_APPROVED)
                    ->whereNotNull('score')
                    ->join('subjects', 'subjects.id', '=', 'grades.subject_id')
                    ->select('subjects.subject_code', 'subjects.title', DB::raw('ROUND(AVG(grades.score), 1) as avg_score'))
                    ->groupBy('subjects.id', 'subjects.subject_code', 'subjects.title')
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
            $attendanceRates = array_column($attendanceByMonth, 'rate');
            $studentInsights['attendanceTrend'] = [
                ...$this->buildTrendInsight(
                    (float) ($attendanceRates[count($attendanceRates) - 1] ?? 0),
                    (float) ($attendanceRates[count($attendanceRates) - 2] ?? 0)
                ),
                'currentLabel' => 'This month',
                'previousLabel' => 'Last month',
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

            $subjectScoreRows = $gradesBySubject
                ->map(function ($row) {
                    return [
                        'subjectCode' => (string) $row->subject_code,
                        'title' => (string) ($row->title ?? ''),
                        'avgScore' => (float) ($row->avg_score ?? 0),
                    ];
                })
                ->values();
            $overallAverage = $subjectScoreRows->avg('avgScore');
            $riskSubjects = collect();

            if ($overallAverage !== null) {
                $riskSubjects = $subjectScoreRows
                    ->filter(fn ($row) => $row['avgScore'] <= ($overallAverage - 10) && $row['avgScore'] < 60)
                    ->sortBy('avgScore')
                    ->take(3)
                    ->values();
            }

            if ($riskSubjects->isEmpty()) {
                $riskSubjects = $subjectScoreRows
                    ->filter(fn ($row) => $row['avgScore'] < 50)
                    ->sortBy('avgScore')
                    ->take(3)
                    ->values();
            }

            $studentInsights['riskSubjects'] = $riskSubjects
                ->map(function ($row) use ($overallAverage) {
                    return [
                        'subjectCode' => $row['subjectCode'],
                        'title' => $row['title'],
                        'avgScore' => $row['avgScore'],
                        'gapFromAverage' => $overallAverage !== null
                            ? round(max((float) $overallAverage - $row['avgScore'], 0), 1)
                            : 0,
                    ];
                })
                ->all();
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
            'insights' => $studentInsights,
        ];
    }
}
