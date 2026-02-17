<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\StoreSubjectGradesRequest;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\GradeReviewRequested;
use App\Services\SubjectGradeCalculator;
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
        $studentIds = $students->pluck('id')->all();

        // Fetch existing grades keyed by student_id
        $grades = Grade::where('subject_id', $subject->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        // Batch-calculate suggested grades from assignments to avoid N+1 queries.
        $calculator = new SubjectGradeCalculator();
        $assignmentDataByStudent = $calculator->calculateForSubjectStudents($subject->id, $studentIds);

        $studentData = $students->map(function ($student) use ($grades, $assignmentDataByStudent) {
            $assignmentData = $assignmentDataByStudent[$student->id] ?? [
                'computed_grade' => null,
                'breakdown' => [],
                'total_assignments' => 0,
                'graded_assignments' => 0,
                'ungraded_assignments' => 0,
                'has_assignments' => false,
            ];

            return [
                'id' => $student->id,
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'photo' => $student->photo,
                'score' => $grades[$student->id]->score ?? null,
                'status' => $grades[$student->id]->status ?? null,
                'rejection_reason' => $grades[$student->id]->rejection_reason ?? null,
                // Assignment-based computed grade
                'computed_grade' => $assignmentData['computed_grade'],
                'assignment_breakdown' => $assignmentData['breakdown'],
                'total_assignments' => $assignmentData['total_assignments'],
                'graded_assignments' => $assignmentData['graded_assignments'],
                'ungraded_assignments' => $assignmentData['ungraded_assignments'],
                'has_assignments' => $assignmentData['has_assignments'],
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
    public function store(StoreSubjectGradesRequest $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validated();

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

    /**
     * Submit final grade for a student based on computed assignment grade or manual input.
     */
    public function submitFinalGrade(Request $request, Subject $subject, Student $student): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        // Verify student is enrolled in the subject's course
        if (! $subject->course->students()->where('students.id', $student->id)->exists()) {
            abort(403, 'Student is not enrolled in this course.');
        }

        $request->validate([
            'use_computed' => 'sometimes|boolean',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        $useComputed = $request->boolean('use_computed');
        $score = null;

        if (! $useComputed && ($request->input('score') === null || $request->input('score') === '')) {
            return redirect()
                ->back()
                ->withErrors(['score' => 'The score field is required when not using computed grade.']);
        }

        // If use_computed is true, calculate from assignments
        if ($useComputed) {
            $calculator = new SubjectGradeCalculator();
            $assignmentData = $calculator->calculateSuggestedGrade($subject->id, $student->id);

            if ($assignmentData['computed_grade'] === null) {
                return redirect()
                    ->back()
                    ->withErrors(['score' => 'Cannot use computed grade: no assignments have been graded yet.']);
            }

            $score = $assignmentData['computed_grade'];
        } else {
            $score = (float) $request->input('score');
        }

        // Create or update grade as pending
        $grade = Grade::updateOrCreate(
            [
                'subject_id' => $subject->id,
                'student_id' => $student->id,
            ],
            [
                'course_id' => $subject->course_id,
                'graded_by' => $user->id,
                'score' => $score,
                'status' => Grade::STATUS_PENDING,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
            ]
        );

        // Log the submission
        GradeReviewLog::create([
            'grade_id' => $grade->id,
            'performed_by' => $user->id,
            'action' => 'submitted',
            'meta' => [
                'subject_id' => $subject->id,
                'course_id' => $subject->course_id,
                'use_computed' => $useComputed,
                'computed_score' => $useComputed ? $score : null,
            ],
        ]);

        Log::info('grade.final_submitted', [
            'grade_id' => $grade->id,
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'student_id' => $student->id,
            'score' => $score,
            'use_computed' => $useComputed,
            'submitted_by' => $user->id,
        ]);

        // Notify staff/admin users
        $staffUsers = User::where('role', 'staff')->get(['id', 'name', 'email']);
        foreach ($staffUsers as $staff) {
            $staff->notify(new GradeReviewRequested($grade, $student, $subject));
        }

        return redirect()
            ->route('teacher.grades.show', $subject->id)
            ->with('success', "Final grade submitted for {$student->full_name} (Score: {$score}). Awaiting approval.");
    }
}
