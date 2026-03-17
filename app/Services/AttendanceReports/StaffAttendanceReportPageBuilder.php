<?php

namespace App\Services\AttendanceReports;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Services\AttendanceReports\Concerns\BuildsStaffAttendanceReportData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StaffAttendanceReportPageBuilder
{
    use BuildsStaffAttendanceReportData;

    public function __construct(
        private readonly StaffAttendanceReportFiltersResolver $filtersResolver,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(Request $request): array
    {
        $resolved = $this->filtersResolver->resolve($request);
        $filters = $resolved['filters'];
        $courses = $resolved['options']['courses'];
        $subjects = $resolved['options']['subjects'];
        $thresholdContext = $this->resolveThresholdContext($filters, $courses, $subjects);
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
            ->with('user:id,name,email')
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
                    'full_name' => $student->user?->name ?? $student->full_name,
                    'email' => $student->user?->email ?? $student->email,
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
            ->with(['student.user', 'subject.course'])
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
                    'student_name' => $attendance->student->user?->name ?? $attendance->student->full_name,
                    'subject_code' => $attendance->subject?->subject_code ?? 'N/A',
                    'course_code' => $courseCode,
                    'status' => $attendance->status,
                ];
            });

        $trendWeekly = $this->buildWeeklyTrend($filters);
        $trendMonthly = $this->buildMonthlyTrend($filters);
        $sessionDrilldown = $this->buildSessionDrilldown($filters);
        return [
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
            'defaults' => $resolved['defaults'],
            'thresholdContext' => $thresholdContext,
            'options' => $resolved['options'],
        ];
    }
}
