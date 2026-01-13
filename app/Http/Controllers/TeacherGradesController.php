<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\GradePublished;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherGradesController extends Controller
{
    /**
     * List subjects the teacher can grade.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get subjects from courses assigned to this teacher
        $subjects = Subject::whereHas('course', function ($query) use ($user) {
            $query->whereHas('teachers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })
            ->with('course')
            ->orderBy('subject_code')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                ];
            });

        return Inertia::render('Teacher/Grades/Index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show enrolled students and existing grades for a subject.
     */
    public function show(Subject $subject): Response
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to the subject's course
        if (!$user->teachingCourses()->where('courses.id', $subject->course_id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $students = $subject->course->students()
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
            ]);

        // Fetch existing grades keyed by student_id
        $grades = Grade::where('subject_id', $subject->id)
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
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'students' => $studentData,
        ]);
    }

    /**
     * Store or update grades for students in a subject.
     */
    public function store(Request $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to the subject's course
        if (!$user->teachingCourses()->where('courses.id', $subject->course_id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Verify all students are enrolled in the subject's course
        $enrolledStudentIds = $subject->course->students()->pluck('students.id')->toArray();
        foreach ($data['grades'] as $record) {
            if (!in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['grades' => 'One or more students are not enrolled in this course.']);
            }
        }

        // Save grades (idempotent via updateOrCreate) and notify students
        foreach ($data['grades'] as $record) {
            $grade = Grade::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'student_id' => $record['student_id'],
                ],
                [
                    'course_id' => $subject->course_id,
                    'graded_by' => $user->id,
                    'score' => $record['score'],
                ]
            );

            $student = Student::find($record['student_id']);
            if ($student && $student->user) {
                $student->user->notify(new GradePublished($grade));
            }
        }

        return redirect()
            ->route('teacher.grades.show', $subject->id)
            ->with('success', 'Grades saved successfully.');
    }
}
