<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\AttendanceAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a list of subjects the teacher can mark attendance for.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get subjects assigned to this teacher
        $subjects = $user->teachingSubjects()
            ->with('course')
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.course_id',
                'subjects.photo',
            ])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'photo' => $subject->photo,
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                ];
            });

        return Inertia::render('Teacher/Attendance/Index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the form for marking attendance for a specific subject.
     */
    public function show(Subject $subject): Response
    {
        $user = Auth::user();

        // Verify teacher is assigned to this subject
        if (!$user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        // Get enrolled students for the subject's course
        $students = $subject->course->students()
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
                'students.photo',
            ]);

        return Inertia::render('Teacher/Attendance/Mark', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'students' => $students,
        ]);
    }

    /**
     * Store attendance records for a subject.
     */
    public function store(Request $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Verify teacher is assigned to this subject
        if (!$user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => ['required', 'in:present,absent'],
        ]);

        // Verify all students are enrolled in the subject's course
        $enrolledStudentIds = $subject->course->students()->pluck('students.id')->toArray();
        foreach ($data['attendance'] as $record) {
            if (!in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['attendance' => 'One or more students are not enrolled in this course.']);
            }
        }

        // Save or update attendance records and notify students
        foreach ($data['attendance'] as $record) {
            $attendance = Attendance::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'student_id' => $record['student_id'],
                    'date' => $data['date'],
                ],
                [
                    'course_id' => $subject->course_id,
                    'status' => $record['status'],
                ]
            );

            $student = Student::find($record['student_id']);
            if ($student && $student->user) {
                $student->user->notify(new AttendanceAlert($attendance));
            }
        }

        return redirect()
            ->route('teacher.attendance.show', $subject->id)
            ->with('success', 'Attendance marked successfully.');
    }
}
