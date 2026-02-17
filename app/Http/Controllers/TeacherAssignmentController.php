<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\Assignments\GradeSubmissionRequest;
use App\Http\Requests\Teacher\Assignments\StoreAssignmentRequest;
use App\Http\Requests\Teacher\Assignments\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAssignmentController extends Controller
{
    /**
     * List assignments for subjects the teacher teaches.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $subjects = $user->teachingSubjects()
            ->with([
                'course:id,course_code,title',
                'assignments' => function ($query) {
                    $query->orderBy('due_date', 'desc')
                        ->with('creator:id,name')
                        ->withCount('submissions')
                        ->withCount([
                            'submissions as graded_submissions_count' => function ($q) {
                                $q->whereNotNull('score');
                            },
                        ]);
                },
            ])
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.course_id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.photo',
            ])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'photo' => $subject->photo,
                    'course_code' => $subject->course?->course_code,
                    'course_title' => $subject->course?->title,
                    'assignments' => $subject->assignments->map(function ($assignment) {
                        return [
                            'id' => $assignment->id,
                            'title' => $assignment->title,
                            'due_date' => $assignment->due_date->format('Y-m-d'),
                            'status' => $assignment->status,
                            'submissions_count' => $assignment->submissions_count ?? 0,
                            'graded_count' => $assignment->graded_submissions_count ?? 0,
                            'creator_name' => $assignment->creator?->name ?? null,
                        ];
                    }),
                ];
            });

        return Inertia::render('Teacher/Assignments/Index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show assignments for a specific subject.
     */
    public function show(Subject $subject): Response
    {
        $user = Auth::user();

        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $assignments = Assignment::where('subject_id', $subject->id)
            ->with('creator:id,name')
            ->withCount('submissions')
            ->withCount([
                'submissions as graded_submissions_count' => function ($q) {
                    $q->whereNotNull('score');
                },
            ])
            ->orderBy('due_date', 'desc')
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date->format('Y-m-d'),
                    'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                    'max_score' => $assignment->max_score,
                    'status' => $assignment->status,
                    'submissions_count' => $assignment->submissions_count ?? 0,
                    'graded_count' => $assignment->graded_submissions_count ?? 0,
                    'is_overdue' => $assignment->isOverdue(),
                    'creator_name' => $assignment->creator?->name ?? null,
                ];
            });

        return Inertia::render('Teacher/Assignments/Show', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show form to create a new assignment.
     */
    public function create(Subject $subject): Response
    {
        $user = Auth::user();

        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        return Inertia::render('Teacher/Assignments/Create', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_id' => $subject->course_id,
                'course_code' => $subject->course->course_code,
            ],
        ]);
    }

    /**
     * Store a new assignment.
     */
    public function store(StoreAssignmentRequest $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validated();

        Assignment::create([
            ...$data,
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'created_by' => $user->id,
        ]);

        return redirect()
            ->route('teacher.assignments.show', $subject->id)
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Show form to edit an assignment.
     */
    public function edit(Assignment $assignment): Response
    {
        $user = Auth::user();

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only edit your own assignments.');
        }

        return Inertia::render('Teacher/Assignments/Edit', [
            'assignment' => [
                'id' => $assignment->id,
                'subject_id' => $assignment->subject_id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date->format('Y-m-d'),
                'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                'max_score' => $assignment->max_score,
                'status' => $assignment->status,
                'allowed_file_types' => $assignment->allowed_file_types ?? [],
                'max_file_size' => $assignment->max_file_size,
                'subject' => [
                    'id' => $assignment->subject->id,
                    'subject_code' => $assignment->subject->subject_code,
                    'title' => $assignment->subject->title,
                ],
            ],
        ]);
    }

    /**
     * Update an assignment.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $user = Auth::user();

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only update your own assignments.');
        }

        $data = $request->validated();

        $assignment->update($data);

        return redirect()
            ->route('teacher.assignments.show', $assignment->subject_id)
            ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Publish an assignment (make it visible to students).
     */
    public function publish(Assignment $assignment): RedirectResponse
    {
        $user = Auth::user();

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only publish your own assignments.');
        }

        $assignment->update([
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        return back()->with('success', 'Assignment published successfully.');
    }

    /**
     * Delete an assignment.
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        $user = Auth::user();

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only delete your own assignments.');
        }

        // Delete all submission files
        foreach ($assignment->submissions as $submission) {
            if (Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $subjectId = $assignment->subject_id;
        $assignment->delete();

        return redirect()
            ->route('teacher.assignments.show', $subjectId)
            ->with('success', 'Assignment deleted successfully.');
    }

    /**
     * View submissions for an assignment.
     */
    public function submissions(Assignment $assignment): Response
    {
        $user = Auth::user();

        if (! $this->canAccessSubmissions($assignment, $user->id)) {
            abort(403, 'You can only view submissions for assignments in subjects you teach.');
        }

        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->with(['student:id,student_no,full_name,photo', 'grader:id,name'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'student' => [
                        'id' => $submission->student->id,
                        'student_no' => $submission->student->student_no,
                        'full_name' => $submission->student->full_name,
                        'photo' => $submission->student->photo,
                    ],
                    'file_path' => $submission->file_path,
                    'original_filename' => $submission->original_filename,
                    'comments' => $submission->comments,
                    'score' => $submission->score,
                    'feedback' => $submission->feedback,
                    'status' => $submission->status,
                    'graded_by' => $submission->grader?->name,
                    'graded_at' => $submission->graded_at?->format('Y-m-d H:i'),
                    'submitted_at' => $submission->created_at->format('Y-m-d H:i'),
                    'last_submitted_at' => $submission->updated_at?->format('Y-m-d H:i'),
                    'percentage' => $submission->percentage,
                ];
            });

        return Inertia::render('Teacher/Assignments/Submissions', [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'max_score' => $assignment->max_score,
                'subject' => [
                    'id' => $assignment->subject->id,
                    'subject_code' => $assignment->subject->subject_code,
                    'title' => $assignment->subject->title,
                ],
            ],
            'submissions' => $submissions,
        ]);
    }

    /**
     * Grade a submission.
     */
    public function grade(GradeSubmissionRequest $request, AssignmentSubmission $submission): RedirectResponse
    {
        $user = Auth::user();

        if (! $this->canAccessSubmissions($submission->assignment, $user->id)) {
            abort(403, 'You can only grade submissions for assignments in subjects you teach.');
        }

        $data = $request->validated();

        // Ensure score doesn't exceed max_score
        $maxScore = $submission->assignment->max_score;
        if ($data['score'] > $maxScore) {
            return back()->withErrors(['score' => "Score cannot exceed maximum score of {$maxScore}."]);
        }

        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_by' => $user->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        return back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Download a student's submission file (for teacher review).
     */
    public function downloadSubmission(AssignmentSubmission $submission): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();

        if (! $this->canAccessSubmissions($submission->assignment, $user->id)) {
            abort(403, 'You can only download submissions for assignments in subjects you teach.');
        }

        if (! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->original_filename
        );
    }

    private function canAccessSubmissions(Assignment $assignment, int $userId): bool
    {
        if ((int) $assignment->created_by === $userId) {
            return true;
        }

        return $assignment->subject()
            ->whereHas('teachers', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->exists();
    }
}
