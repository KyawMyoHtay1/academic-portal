<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StaffAttendanceReportController extends Controller
{
    /**
     * Display attendance summary report.
     */
    public function index(): InertiaResponse
    {
        // Overall statistics
        $totalRecords = Attendance::count();
        $totalPresent = Attendance::where('status', 'present')->count();
        $totalAbsent = Attendance::where('status', 'absent')->count();
        $attendanceRate = $totalRecords > 0
            ? round(($totalPresent / $totalRecords) * 100, 2)
            : 0;

        // Attendance by course
        $attendanceByCourse = Course::withCount([
            'attendances as total_attendances',
            'attendances as present_attendances' => function ($query) {
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
        $studentsWithLowAttendance = Student::with('courses')
            ->get()
            ->map(function ($student) {
                $attendances = Attendance::where('student_id', $student->id)->get();
                $total = $attendances->count();
                $present = $attendances->where('status', 'present')->count();
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
        $attendanceBySubject = Subject::with('course')
            ->withCount([
                'attendances as total_attendances',
                'attendances as present_attendances' => function ($query) {
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
        $recentRecords = Attendance::with(['student', 'subject.course'])
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->paginate(20)
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
        ]);
    }
}
