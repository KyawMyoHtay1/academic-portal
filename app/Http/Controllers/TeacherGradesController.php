<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\GradeReviewRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Ensure the teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $students = $subject->course->students()
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
                'students.photo',
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
                'photo' => $student->photo,
                'score' => $grades[$student->id]->score ?? null,
                'status' => $grades[$student->id]->status ?? null,
                'rejection_reason' => $grades[$student->id]->rejection_reason ?? null,
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

        // Ensure the teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Verify all students are enrolled in the subject's course
        $enrolledStudentIds = $subject->course->students()->pluck('students.id')->toArray();

        // Filter and save only grades with scores (skip empty ones)
        $savedCount = 0;
        foreach ($data['grades'] as $record) {
            // Skip if student is not enrolled
            if (! in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['grades' => 'One or more students are not enrolled in this course.']);
            }

            // Skip if score is empty or null (but allow 0.0 as a valid grade)
            $score = $record['score'] ?? null;
            if ($score === null || $score === '') {
                continue;
            }

            // Save grade (idempotent via updateOrCreate) and notify student
            $grade = Grade::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'student_id' => $record['student_id'],
                ],
                [
                    'course_id' => $subject->course_id,
                    'graded_by' => $user->id,
                    'score' => $score,
                    // Submit for review; staff/admin will approve to publish.
                    'status' => Grade::STATUS_PENDING,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                ]
            );

            GradeReviewLog::create([
                'grade_id' => $grade->id,
                'performed_by' => $user->id,
                'action' => 'submitted',
                'meta' => [
                    'subject_id' => $subject->id,
                    'course_id' => $subject->course_id,
                ],
            ]);

            Log::info('grade.review_submitted', [
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
                'course_id' => $subject->course_id,
                'student_id' => (int) $record['student_id'],
                'submitted_by' => $user->id,
            ]);

            // Notify staff/admin users that a grade needs review.
            $student = Student::find($record['student_id']);
            $staffUsers = User::where('role', 'staff')->get(['id', 'name', 'email']);
            foreach ($staffUsers as $staff) {
                $staff->notify(new GradeReviewRequested($grade, $student, $subject));
            }

            $savedCount++;
        }

        if ($savedCount > 0) {
            return redirect()
                ->route('teacher.grades.show', $subject->id)
                ->with('success', "Successfully submitted {$savedCount} grade(s) for review.");
        }

        return redirect()
            ->route('teacher.grades.show', $subject->id)
            ->with('info', 'No grades were saved. Please enter at least one score.');
    }
}
