<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Subject;
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
        ]);
    }
}
