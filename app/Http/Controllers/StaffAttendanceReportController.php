<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StaffAttendanceReportController extends Controller
{
    /**
     * Display attendance summary report.
     */
    public function index(Request $request): InertiaResponse
    {
        [$filters, $programmes, $intakeYears, $semesters] = $this->resolveReportFilters($request);

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
            ->having('total_attendances', '>', 0)
            ->orderBy('course_code')
            ->get()
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

        // Students with low attendance (< 75%)
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
            ->map(function ($student) use ($filters) {
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
                ];
            })
            ->filter(function ($student) {
                return $student['rate'] !== null && $student['rate'] < 75;
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
            ->having('total_attendances', '>', 0)
            ->orderBy('subject_code')
            ->get()
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
            'options' => [
                'programmes' => $programmes,
                'intakeYears' => $intakeYears,
                'semesters' => $semesters,
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
     * @param  array<string, string>  $filters
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
    }

    /**
     * Resolve and normalize report filters and options.
     *
     * @return array{0: array<string, string>, 1: \Illuminate\Support\Collection<int, string>, 2: \Illuminate\Support\Collection<int, int>, 3: \Illuminate\Support\Collection<int, string>}
     */
    private function resolveReportFilters(Request $request): array
    {
        $filters = [
            'programme' => trim((string) $request->input('programme', 'all')),
            'intake_year' => trim((string) $request->input('intake_year', 'all')),
            'semester' => trim((string) $request->input('semester', 'all')),
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

        if ($filters['programme'] !== 'all' && ! $programmes->contains($filters['programme'])) {
            $filters['programme'] = 'all';
        }
        if ($filters['intake_year'] !== 'all' && ! $intakeYears->map(fn ($year) => (string) $year)->contains($filters['intake_year'])) {
            $filters['intake_year'] = 'all';
        }
        if ($filters['semester'] !== 'all' && ! $semesters->contains($filters['semester'])) {
            $filters['semester'] = 'all';
        }

        return [$filters, $programmes, $intakeYears, $semesters];
    }
}
