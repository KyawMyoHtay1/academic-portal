<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a list of courses the teacher can mark attendance for.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get courses assigned to this teacher
        $courses = $user->teachingCourses()
            ->orderBy('courses.course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
            ]);

        return Inertia::render('Teacher/Attendance/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for marking attendance for a specific course.
     */
    public function show(Course $course): Response
    {
        $user = Auth::user();

        // Verify teacher is assigned to this course
        if (!$user->teachingCourses()->where('courses.id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        // Get enrolled students for this course
        $students = $course->students()
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
            ]);

        return Inertia::render('Teacher/Attendance/Mark', [
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
            ],
            'students' => $students,
        ]);
    }

    /**
     * Store attendance records for a course.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();

        // Verify teacher is assigned to this course
        if (!$user->teachingCourses()->where('courses.id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        $data = $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => ['required', 'in:present,absent'],
        ]);

        // Verify all students are enrolled in the course
        $enrolledStudentIds = $course->students()->pluck('students.id')->toArray();
        foreach ($data['attendance'] as $record) {
            if (!in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['attendance' => 'One or more students are not enrolled in this course.']);
            }
        }

        // Save or update attendance records
        foreach ($data['attendance'] as $record) {
            Attendance::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'student_id' => $record['student_id'],
                    'date' => $data['date'],
                ],
                [
                    'status' => $record['status'],
                ]
            );
        }

        return redirect()
            ->route('teacher.attendance.show', $course->id)
            ->with('success', 'Attendance marked successfully.');
    }
}
