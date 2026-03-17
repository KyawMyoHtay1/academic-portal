<?php

namespace App\Services\Dashboard;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
use App\Services\Dashboard\Concerns\BuildsDashboardTrendInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StaffDashboardDataBuilder
{
    use BuildsDashboardTrendInsight;

    /**
     * @return array<string, mixed>
     */
    public function build(\DateTimeInterface $cacheTtl): array
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
        $failedJobs = 0;
        if ($queueConnection === 'database') {
            try {
                $pendingJobs = DB::table('jobs')->count();
            } catch (\Exception $e) {
                // Jobs table might not exist.
            }

            try {
                $failedJobs = DB::table('failed_jobs')->count();
            } catch (\Exception $e) {
                // Failed jobs table might not exist.
            }
        }

        $schedulerConfigured = true;
        $queueHealthy = $queueConfigured && $schedulerConfigured && $failedJobs === 0;
        $alertSystemStatus = [
            'queueConfigured' => $queueConfigured,
            'queueConnection' => $queueConnection,
            'pendingJobs' => $pendingJobs,
            'failedJobs' => $failedJobs,
            'schedulerConfigured' => $schedulerConfigured,
            'status' => $queueHealthy ? 'ready' : 'warning',
            'message' => $queueConfigured
                ? ($failedJobs > 0
                    ? 'Failed jobs detected - review queue failures'
                    : ($pendingJobs > 10
                        ? 'Queue worker may need attention (many pending jobs)'
                        : 'System ready for automatic alerts'))
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
        $pendingFees = Cache::remember('dashboard:staff:pending_fees_total', $cacheTtl, fn () => Fee::whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_FAILED])->sum('amount'));

        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth()->toDateString();
        $currentMonthEnd = $now->copy()->endOfMonth()->toDateString();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();
        $currentWeekStart = $now->copy()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $currentWeekStart->copy()->subSecond();

        $feesCollectedThisMonth = Cache::remember('dashboard:staff:fees_collected_this_month', $cacheTtl, function () use ($currentMonthStart, $currentMonthEnd) {
            return Fee::where('status', Fee::STATUS_PAID)
                ->whereBetween('paid_date', [$currentMonthStart, $currentMonthEnd])
                ->sum('amount');
        });
        $feesCollectedLastMonth = Cache::remember('dashboard:staff:fees_collected_last_month', $cacheTtl, function () use ($previousMonthStart, $previousMonthEnd) {
            return Fee::where('status', Fee::STATUS_PAID)
                ->whereBetween('paid_date', [$previousMonthStart, $previousMonthEnd])
                ->sum('amount');
        });

        $pendingGradesThisWeek = Cache::remember('dashboard:staff:pending_grades_this_week', $cacheTtl, function () use ($currentWeekStart, $now) {
            return Grade::where('status', Grade::STATUS_PENDING)
                ->whereBetween('updated_at', [$currentWeekStart, $now])
                ->count();
        });
        $pendingGradesLastWeek = Cache::remember('dashboard:staff:pending_grades_last_week', $cacheTtl, function () use ($previousWeekStart, $previousWeekEnd) {
            return Grade::where('status', Grade::STATUS_PENDING)
                ->whereBetween('updated_at', [$previousWeekStart, $previousWeekEnd])
                ->count();
        });

        $approvalsThisWeek = Cache::remember('dashboard:staff:approvals_this_week', $cacheTtl, function () use ($currentWeekStart, $now) {
            return DB::table('course_student')
                ->where('status', 'approved')
                ->whereBetween('updated_at', [$currentWeekStart, $now])
                ->count();
        });
        $approvalsLastWeek = Cache::remember('dashboard:staff:approvals_last_week', $cacheTtl, function () use ($previousWeekStart, $previousWeekEnd) {
            return DB::table('course_student')
                ->where('status', 'approved')
                ->whereBetween('updated_at', [$previousWeekStart, $previousWeekEnd])
                ->count();
        });

        $staffInsights = [
            'feesCollectedTrend' => [
                ...$this->buildTrendInsight((float) $feesCollectedThisMonth, (float) $feesCollectedLastMonth),
                'currentLabel' => 'This month',
                'previousLabel' => 'Last month',
            ],
            'pendingGradesTrend' => [
                ...$this->buildTrendInsight((float) $pendingGradesThisWeek, (float) $pendingGradesLastWeek),
                'currentLabel' => 'This week',
                'previousLabel' => 'Last week',
            ],
            'approvalsTrend' => [
                ...$this->buildTrendInsight((float) $approvalsThisWeek, (float) $approvalsLastWeek),
                'currentLabel' => 'This week',
                'previousLabel' => 'Last week',
            ],
        ];

        $feeStatusCounts = Cache::remember('dashboard:staff:fee_status_counts', $cacheTtl, function () {
            return Fee::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
        $chartsFeeStatus = [
            'labels' => ['Pending', 'Payment Pending', 'Failed', 'Paid'],
            'datasets' => [[
                'data' => [
                    $feeStatusCounts['pending'] ?? 0,
                    $feeStatusCounts['payment_pending'] ?? 0,
                    $feeStatusCounts['failed'] ?? 0,
                    $feeStatusCounts['paid'] ?? 0,
                ],
                'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
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
            'insights' => $staffInsights,
            'alertSystemStatus' => $alertSystemStatus,
        ];
    }
}
