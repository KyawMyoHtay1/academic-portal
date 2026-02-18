<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentAttendanceController extends Controller
{
    /**
     * Display attendance report for the authenticated student.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return Inertia::render('Student/Attendance/Index', [
                'overall' => [
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'rate' => 0,
                ],
                'byCourse' => [],
                'bySubject' => [],
                'recentRecords' => [],
                'trendWeekly' => [],
                'subjectRisk' => [],
                'message' => 'No student record found. Please contact administration.',
            ]);
        }

        // Overall statistics
        $attendances = Attendance::where('student_id', $student->id)->get();
        $totalRecords = $attendances->count();
        $totalPresent = $attendances->where('status', 'present')->count();
        $totalAbsent = $totalRecords - $totalPresent;
        $attendanceRate = $totalRecords > 0
            ? round(($totalPresent / $totalRecords) * 100, 2)
            : 0;

        // Attendance by course
        $attendanceByCourse = $student->courses()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->whereHas('attendances', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'attendances as present_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->where('status', 'present');
                },
            ])
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

        // Attendance by subject
        $attendanceBySubject = Subject::whereHas('attendances', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with('course')
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'attendances as present_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->where('status', 'present');
                },
            ])
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
                    'course_code' => $subject->course->course_code ?? 'N/A',
                    'total' => $subject->total_attendances,
                    'present' => $subject->present_attendances,
                    'absent' => $subject->total_attendances - $subject->present_attendances,
                    'rate' => $rate,
                ];
            });

        // Recent attendance records (last 30 days)
        $recentRecords = Attendance::where('student_id', $student->id)
            ->with('subject.course')
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($attendance) {
                $courseCode = $attendance->subject?->course?->course_code
                    ?? 'N/A';

                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date->format('Y-m-d'),
                    'subject_code' => $attendance->subject?->subject_code ?? 'N/A',
                    'subject_title' => $attendance->subject?->title ?? 'N/A',
                    'course_code' => $courseCode,
                    'status' => $attendance->status,
                ];
            });

        // Last 12 weeks trend (lightweight reporting for student dashboard view)
        $firstWeek = now()->startOfWeek()->subWeeks(11);
        $trendRows = Attendance::query()
            ->where('student_id', $student->id)
            ->whereDate('date', '>=', $firstWeek)
            ->get(['date', 'status']);

        $trendByWeek = $trendRows->groupBy(function ($attendance) {
            return $attendance->date->copy()->startOfWeek()->toDateString();
        });

        $trendWeekly = collect(range(0, 11))
            ->map(function ($offset) use ($firstWeek, $trendByWeek) {
                $weekStart = $firstWeek->copy()->addWeeks($offset);
                $weekKey = $weekStart->toDateString();
                $rows = $trendByWeek->get($weekKey, collect());
                $total = $rows->count();
                $present = $rows->where('status', 'present')->count();
                $absent = max($total - $present, 0);

                return [
                    'week_start' => $weekKey,
                    'label' => $weekStart->format('M d'),
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $subjectRisk = $attendanceBySubject
            ->filter(function ($subject) {
                return (int) ($subject['total'] ?? 0) > 0 && (float) ($subject['rate'] ?? 0) < 75;
            })
            ->sortBy('rate')
            ->take(5)
            ->values();

        return Inertia::render('Student/Attendance/Index', [
            'overall' => [
                'total' => $totalRecords,
                'present' => $totalPresent,
                'absent' => $totalAbsent,
                'rate' => $attendanceRate,
            ],
            'byCourse' => $attendanceByCourse,
            'bySubject' => $attendanceBySubject,
            'recentRecords' => $recentRecords,
            'trendWeekly' => $trendWeekly,
            'subjectRisk' => $subjectRisk,
        ]);
    }

    /**
     * Export personal attendance report for the authenticated student.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
            return redirect()
                ->route('student.attendance.index')
                ->with('error', 'No student record found.');
        }

        $records = Attendance::where('student_id', $student->id)
            ->with('subject.course')
            ->orderByDesc('date')
            ->get()
            ->map(function ($attendance) {
                $courseCode = $attendance->subject?->course?->course_code ?? 'N/A';

                return [
                    'date' => $attendance->date?->format('Y-m-d'),
                    'subject_code' => $attendance->subject?->subject_code ?? 'N/A',
                    'subject_title' => $attendance->subject?->title ?? 'N/A',
                    'course_code' => $courseCode,
                    'status' => $attendance->status,
                ];
            });

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        $timestamp = now()->format('Ymd_His');
        $studentNo = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $student->student_no);
        $studentNo = $studentNo !== '' ? $studentNo : 'student';

        if ($format === 'csv') {
            $filename = "attendance_{$studentNo}_{$timestamp}.csv";

            return response()->streamDownload(function () use ($records): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Date',
                    'Subject Code',
                    'Subject Title',
                    'Course Code',
                    'Status',
                ]);

                foreach ($records as $row) {
                    fputcsv($handle, [
                        $row['date'],
                        $row['subject_code'],
                        $row['subject_title'],
                        $row['course_code'],
                        $row['status'],
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // Reuse summary tables for a printable personal report.
        $byCourse = $student->courses()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->whereHas('attendances', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'attendances as present_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->where('status', 'present');
                },
            ])
            ->orderBy('course_code')
            ->get()
            ->map(function ($course) {
                $courseTotal = (int) ($course->total_attendances ?? 0);
                $coursePresent = (int) ($course->present_attendances ?? 0);
                return [
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'total' => $courseTotal,
                    'present' => $coursePresent,
                    'absent' => max($courseTotal - $coursePresent, 0),
                    'rate' => $courseTotal > 0 ? round(($coursePresent / $courseTotal) * 100, 2) : 0,
                ];
            });

        $bySubject = Subject::whereHas('attendances', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with('course')
            ->withCount([
                'attendances as total_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'attendances as present_attendances' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->where('status', 'present');
                },
            ])
            ->orderBy('subject_code')
            ->get()
            ->map(function ($subject) {
                $subjectTotal = (int) ($subject->total_attendances ?? 0);
                $subjectPresent = (int) ($subject->present_attendances ?? 0);
                return [
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course->course_code ?? 'N/A',
                    'total' => $subjectTotal,
                    'present' => $subjectPresent,
                    'absent' => max($subjectTotal - $subjectPresent, 0),
                    'rate' => $subjectTotal > 0 ? round(($subjectPresent / $subjectTotal) * 100, 2) : 0,
                ];
            });

        $pdf = Pdf::loadView('attendance.student_report', [
            'student' => [
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'programme' => $student->programme,
                'intake_year' => $student->intake_year,
            ],
            'overall' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ],
            'byCourse' => $byCourse,
            'bySubject' => $bySubject,
            'rows' => $records,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("attendance_{$studentNo}_{$timestamp}.pdf");
    }
}
