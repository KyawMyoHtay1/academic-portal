<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherGradesController extends Controller
{
    /**
     * List courses the teacher can grade.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $courses = $user->teachingCourses()
            ->orderBy('courses.course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
            ]);

        return Inertia::render('Teacher/Grades/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show enrolled students and existing grades for a course.
     */
    public function show(Course $course): Response
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to this course
        if (!$user->teachingCourses()->where('courses.id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        $students = $course->students()
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
            ]);

        // Fetch existing grades keyed by student_id
        $grades = Grade::where('course_id', $course->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $studentData = $students->map(function ($student) use ($grades) {
            return [
                'id' => $student->id,
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'score' => $grades[$student->id]->score ?? null,
            ];
        });

        return Inertia::render('Teacher/Grades/Mark', [
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
            ],
            'students' => $studentData,
        ]);
    }

    /**
     * Store or update grades for students in a course.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to this course
        if (!$user->teachingCourses()->where('courses.id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Verify all students are enrolled in the course
        $enrolledStudentIds = $course->students()->pluck('students.id')->toArray();
        foreach ($data['grades'] as $record) {
            if (!in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['grades' => 'One or more students are not enrolled in this course.']);
            }
        }

        // Save grades (idempotent via updateOrCreate)
        foreach ($data['grades'] as $record) {
            Grade::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'student_id' => $record['student_id'],
                ],
                [
                    'graded_by' => $user->id,
                    'score' => $record['score'],
                ]
            );
        }

        return redirect()
            ->route('teacher.grades.show', $course->id)
            ->with('success', 'Grades saved successfully.');
    }
}
