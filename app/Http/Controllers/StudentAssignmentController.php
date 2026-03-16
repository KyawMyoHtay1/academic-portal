<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\Assignments\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Notifications\AssignmentSubmissionReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StudentAssignmentController extends Controller
{
    /**
     * List assignments for the authenticated student.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return Inertia::render('Student/Assignments/Index', [
                'assignments' => [],
                'message' => 'No student record found. Please contact administration.',
            ]);
        }

        // Get assignments for courses the student is enrolled in
        // Keep this consistent with "My Courses" (approved + withdrawal_pending)
        $enrolledCourseIds = $student->courses()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->pluck('courses.id');

        $assignments = Assignment::query()
            ->whereHas('subject', function ($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds);
            })
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->with(['subject.course', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($assignment) {
                $submission = $assignment->submissions->first();
                $isGradedSubmission = $this->isSubmissionGraded($submission);
                $canSubmit = $assignment->canSubmit() && ! $isGradedSubmission;

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date->format('Y-m-d'),
                    'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                    'max_score' => $assignment->max_score,
                    'subject' => [
                        'id' => $assignment->subject->id,
                        'subject_code' => $assignment->subject->subject_code,
                        'title' => $assignment->subject->title,
                    ],
                    'course' => [
                        'id' => $assignment->course->id,
                        'course_code' => $assignment->course->course_code,
                        'title' => $assignment->course->title,
                    ],
                    'is_overdue' => $assignment->isOverdue(),
                    'can_submit' => $canSubmit,
                    'resubmission_locked_by_grading' => $isGradedSubmission,
                    'submission' => $submission ? [
                        'id' => $submission->id,
                        'file_path' => $submission->file_path,
                        'original_filename' => $submission->original_filename,
                        'score' => $submission->score,
                        'feedback' => $submission->feedback,
                        'status' => $submission->status,
                        'submitted_at' => $submission->created_at?->toIso8601String(),
                        'graded_at' => $submission->graded_at?->toIso8601String(),
                        'percentage' => $submission->percentage,
                    ] : null,
                ];
            });

        return Inertia::render('Student/Assignments/Index', [
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show a specific assignment with submission form.
     */
    public function show(Assignment $assignment): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(404);
        }

        // Students should not access draft/closed assignments directly by URL.
        if ($assignment->status !== Assignment::STATUS_PUBLISHED) {
            abort(404);
        }

        $assignment->loadMissing('subject.course');

        // Check if student is enrolled in the assignment's course
        $isEnrolled = $student->courses()
            ->where('courses.id', $assignment->subject?->course_id)
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();
        $isGradedSubmission = $this->isSubmissionGraded($submission);
        $canSubmit = $assignment->canSubmit() && ! $isGradedSubmission;

        return Inertia::render('Student/Assignments/Show', [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date->format('Y-m-d'),
                'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                'max_score' => $assignment->max_score,
                'allowed_file_types' => $assignment->allowed_file_types ?? ['pdf', 'doc', 'docx'],
                'max_file_size' => $assignment->max_file_size ?? 5120, // Default 5MB
                'subject' => [
                    'id' => $assignment->subject->id,
                    'subject_code' => $assignment->subject->subject_code,
                    'title' => $assignment->subject->title,
                ],
                'course' => [
                    'id' => $assignment->course->id,
                    'course_code' => $assignment->course->course_code,
                    'title' => $assignment->course->title,
                ],
                'is_overdue' => $assignment->isOverdue(),
                'can_submit' => $canSubmit,
                'resubmission_locked_by_grading' => $isGradedSubmission,
            ],
            'submission' => $submission ? [
                'id' => $submission->id,
                'file_path' => $submission->file_path,
                'original_filename' => $submission->original_filename,
                'comments' => $submission->comments,
                'score' => $submission->score,
                'feedback' => $submission->feedback,
                'status' => $submission->status,
                'submitted_at' => $submission->created_at?->toIso8601String(),
                'graded_at' => $submission->graded_at?->toIso8601String(),
                'percentage' => $submission->percentage,
                'grader' => $submission->grader?->name,
            ] : null,
        ]);
    }

    /**
     * Submit an assignment.
     */
    public function submit(SubmitAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(404);
        }

        $assignment->loadMissing('subject.course');

        // Check if student is enrolled
        $isEnrolled = $student->courses()
            ->where('courses.id', $assignment->subject?->course_id)
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Check if already submitted (allow resubmission only when not graded and before due date)
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($this->isSubmissionGraded($existingSubmission)) {
            return back()->withErrors([
                'file' => 'Resubmission is not allowed because this assignment has already been graded.',
            ]);
        }

        // Check if assignment is still open for submission
        if (! $assignment->canSubmit()) {
            return back()->withErrors(['file' => 'This assignment is no longer accepting submissions.']);
        }

        $data = $request->validated();

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // Validate file type
        $allowedTypes = $assignment->allowed_file_types ?? ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];
        if (! in_array($extension, $allowedTypes)) {
            return back()->withErrors([
                'file' => 'File type not allowed. Allowed types: '.implode(', ', $allowedTypes),
            ]);
        }

        // Validate file size (in KB)
        $maxSizeKB = $assignment->max_file_size ?? 5120; // Default 5MB
        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $maxSizeKB) {
            return back()->withErrors([
                'file' => "File size exceeds maximum allowed size of {$maxSizeKB}KB.",
            ]);
        }

        // Store file
        $filename = 'assignments/'.uniqid().'.'.$extension;
        Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));

        if ($existingSubmission) {
            // Delete old file (if exists)
            if ($existingSubmission->file_path && Storage::disk('public')->exists($existingSubmission->file_path)) {
                Storage::disk('public')->delete($existingSubmission->file_path);
            }

            // Resubmission resets grading info so teacher grades latest file.
            $existingSubmission->update([
                'file_path' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'comments' => $data['comments'] ?? null,
                'score' => null,
                'feedback' => null,
                'graded_by' => null,
                'graded_at' => null,
                'status' => AssignmentSubmission::STATUS_SUBMITTED,
            ]);

            $successMessage = 'Assignment resubmitted successfully.';
            $isResubmission = true;
        } else {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'file_path' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'comments' => $data['comments'] ?? null,
                'status' => AssignmentSubmission::STATUS_SUBMITTED,
            ]);

            $successMessage = 'Assignment submitted successfully.';
            $isResubmission = false;
        }

        $teacherRecipient = $assignment->creator;
        if ($teacherRecipient && $teacherRecipient->id !== $user->id) {
            $teacherRecipient->notify(new AssignmentSubmissionReceived(
                assignment: $assignment,
                student: $student,
                isResubmission: $isResubmission
            ));
        }

        return redirect()
            ->route('student.assignments.show', $assignment->id)
            ->with('success', $successMessage);
    }

    /**
     * Download a submitted file (for student to view their own submission).
     */
    public function download(AssignmentSubmission $submission): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student || $submission->student_id !== $student->id) {
            abort(403, 'You can only download your own submissions.');
        }

        if (! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->original_filename
        );
    }

    private function isSubmissionGraded(?AssignmentSubmission $submission): bool
    {
        if (! $submission) {
            return false;
        }

        return $submission->status === AssignmentSubmission::STATUS_GRADED
            || $submission->score !== null
            || $submission->graded_at !== null;
    }
}
