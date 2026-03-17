<?php

namespace App\Services\Dashboard;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use App\Services\Dashboard\Concerns\BuildsDashboardTrendInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TeacherDashboardDataBuilder
{
    use BuildsDashboardTrendInsight;

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, \DateTimeInterface $cacheTtl): array
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

        $now = Carbon::now();
        $currentWeekStart = $now->copy()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $currentWeekStart->copy()->subSecond();

        $pendingGradesThisWeek = Cache::remember("dashboard:teacher:{$user->id}:pending_grades_this_week", $cacheTtl, function () use ($subjectIdArray, $currentWeekStart, $now) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->where('status', Grade::STATUS_PENDING)
                ->whereBetween('updated_at', [$currentWeekStart, $now])
                ->count();
        });
        $pendingGradesLastWeek = Cache::remember("dashboard:teacher:{$user->id}:pending_grades_last_week", $cacheTtl, function () use ($subjectIdArray, $previousWeekStart, $previousWeekEnd) {
            return Grade::whereIn('subject_id', $subjectIdArray)
                ->where('status', Grade::STATUS_PENDING)
                ->whereBetween('updated_at', [$previousWeekStart, $previousWeekEnd])
                ->count();
        });

        $needsGradingSubmissions = Cache::remember("dashboard:teacher:{$user->id}:needs_grading_submissions", $cacheTtl, function () use ($user) {
            return AssignmentSubmission::whereHas('assignment', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
                ->whereNull('score')
                ->count();
        });
        $gradedSubmissions = Cache::remember("dashboard:teacher:{$user->id}:graded_submissions", $cacheTtl, function () use ($user) {
            return AssignmentSubmission::whereHas('assignment', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
                ->whereNotNull('score')
                ->count();
        });

        $atRiskStudents = Cache::remember("dashboard:teacher:{$user->id}:at_risk_students", $cacheTtl, function () use ($subjectIdArray) {
            if (count($subjectIdArray) === 0) {
                return [];
            }

            return Attendance::query()
                ->join('students', 'students.id', '=', 'attendances.student_id')
                ->leftJoin('users', 'users.id', '=', 'students.user_id')
                ->whereIn('attendances.subject_id', $subjectIdArray)
                ->selectRaw('
                    students.id as student_id,
                    students.student_no,
                    users.name as full_name,
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN attendances.status = "present" THEN 1 ELSE 0 END) as present_sessions
                ')
                ->groupBy('students.id', 'students.student_no', 'users.name')
                ->havingRaw('COUNT(*) >= 3')
                ->get()
                ->map(function ($row) {
                    $total = (int) ($row->total_sessions ?? 0);
                    $present = (int) ($row->present_sessions ?? 0);
                    $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                    return [
                        'student_id' => (int) $row->student_id,
                        'student_no' => (string) $row->student_no,
                        'name' => (string) $row->full_name,
                        'attendanceRate' => $rate,
                        'totalSessions' => $total,
                        'reason' => $rate < 60 ? 'Critical attendance risk' : 'Below attendance threshold',
                    ];
                })
                ->filter(fn ($row) => $row['attendanceRate'] < 75)
                ->sortBy('attendanceRate')
                ->take(5)
                ->values()
                ->all();
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

        $attendanceRates = array_column($attendanceByMonth, 'rate');
        $teacherInsights = [
            'pendingGradesTrend' => [
                ...$this->buildTrendInsight((float) $pendingGradesThisWeek, (float) $pendingGradesLastWeek),
                'currentLabel' => 'This week',
                'previousLabel' => 'Last week',
            ],
            'attendanceTrend' => [
                ...$this->buildTrendInsight(
                    (float) ($attendanceRates[count($attendanceRates) - 1] ?? 0),
                    (float) ($attendanceRates[count($attendanceRates) - 2] ?? 0)
                ),
                'currentLabel' => 'This month',
                'previousLabel' => 'Last month',
            ],
            'needsGradingSubmissions' => (int) $needsGradingSubmissions,
            'gradedSubmissions' => (int) $gradedSubmissions,
            'atRiskStudents' => $atRiskStudents,
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
            'insights' => $teacherInsights,
        ];
    }
}
