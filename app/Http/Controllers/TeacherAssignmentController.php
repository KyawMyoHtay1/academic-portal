<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\Assignments\GradeSubmissionRequest;
use App\Http\Requests\Teacher\Assignments\StoreAssignmentRequest;
use App\Http\Requests\Teacher\Assignments\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use App\Notifications\AssignmentSubmissionGraded;
use App\Notifications\AssignmentUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

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

        $expectedStudentsCount = $subject->course
            ->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->count();

        $assignments = Assignment::where('subject_id', $subject->id)
            ->with('creator:id,name')
            ->withCount('submissions')
            ->withCount([
                'submissions as graded_submissions_count' => function ($q) {
                    $q->whereNotNull('score');
                },
            ])
            ->orderBy('due_date', 'desc')
            ->get();

        $assignmentIds = $assignments->pluck('id')->all();
        $lateCounts = AssignmentSubmission::query()
            ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')
            ->whereIn('assignment_submissions.assignment_id', $assignmentIds)
            ->whereRaw("assignment_submissions.updated_at > CONCAT(assignments.due_date, ' ', COALESCE(assignments.due_time, '23:59:59'))")
            ->selectRaw('assignment_submissions.assignment_id, COUNT(*) as late_count')
            ->groupBy('assignment_submissions.assignment_id')
            ->pluck('late_count', 'assignment_submissions.assignment_id');

        $assignments = $assignments
            ->map(function ($assignment) use ($expectedStudentsCount, $lateCounts, $user) {
                $submissionsCount = (int) ($assignment->submissions_count ?? 0);
                $gradedCount = (int) ($assignment->graded_submissions_count ?? 0);
                $expectedCount = max($expectedStudentsCount, 0);
                $isOwner = (int) ($assignment->created_by ?? 0) === (int) ($user->id ?? 0);

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date->format('Y-m-d'),
                    'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                    'max_score' => $assignment->max_score,
                    'status' => $assignment->status,
                    'submissions_count' => $submissionsCount,
                    'graded_count' => $gradedCount,
                    'expected_students_count' => $expectedCount,
                    'missing_submissions_count' => max($expectedCount - $submissionsCount, 0),
                    'late_submissions_count' => (int) ($lateCounts[$assignment->id] ?? 0),
                    'submitted_percent' => $expectedCount > 0
                        ? min(100, round(($submissionsCount / $expectedCount) * 100))
                        : 0,
                    'graded_percent' => $expectedCount > 0
                        ? min(100, round(($gradedCount / $expectedCount) * 100))
                        : 0,
                    'is_overdue' => $assignment->isOverdue(),
                    'can_manage' => $isOwner,
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

        $assignment = Assignment::create([
            ...$data,
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'created_by' => $user->id,
        ]);

        if ($assignment->status === Assignment::STATUS_PUBLISHED) {
            $this->notifyEnrolledStudents($assignment, 'created');
        }

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

        if ($assignment->status === Assignment::STATUS_PUBLISHED) {
            $this->notifyEnrolledStudents($assignment, 'updated');
        }

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

        if ($assignment->status === Assignment::STATUS_PUBLISHED) {
            return back()->with('info', 'Assignment is already published.');
        }

        $assignment->update([
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        $this->notifyEnrolledStudents($assignment, 'published');

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

        if ($assignment->created_by !== $user->id) {
            $creatorName = $assignment->creator?->name ?? 'another teacher';
            abort(
                403,
                "You can only view submissions for your own assignments. This assignment was created by {$creatorName}."
            );
        }

        $dueAt = $this->assignmentDueAt($assignment);

        $expectedStudents = $assignment->course
            ->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->orderBy('students.full_name')
            ->get(['students.id', 'students.student_no', 'students.full_name', 'students.photo'])
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->full_name,
                    'photo' => $student->photo,
                ];
            });

        $submissionModels = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->with(['student:id,student_no,full_name,photo', 'grader:id,name'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $submissions = $submissionModels
            ->map(function ($submission) use ($dueAt) {
                $submittedAt = $submission->updated_at ?? $submission->created_at;
                $isLate = $dueAt !== null && $submittedAt !== null
                    ? $submittedAt->gt($dueAt)
                    : false;
                $lateByMinutes = $isLate && $submittedAt !== null
                    ? $dueAt->diffInMinutes($submittedAt)
                    : 0;

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
                    'graded_at' => $submission->graded_at?->toIso8601String(),
                    'submitted_at' => $submission->created_at?->toIso8601String(),
                    'last_submitted_at' => $submission->updated_at?->toIso8601String(),
                    'percentage' => $submission->percentage,
                    'is_late' => $isLate,
                    'late_by' => $isLate ? $this->formatLateDuration($lateByMinutes) : null,
                ];
            });

        $submittedStudentIds = $submissionModels
            ->pluck('student_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $missingStudents = $expectedStudents
            ->filter(function ($student) use ($submittedStudentIds) {
                return ! in_array((int) $student['id'], $submittedStudentIds, true);
            })
            ->values()
            ->all();

        $lateSubmissions = $submissions
            ->filter(fn ($submission) => (bool) ($submission['is_late'] ?? false))
            ->map(function ($submission) {
                return [
                    'id' => $submission['id'],
                    'student_no' => $submission['student']['student_no'] ?? 'N/A',
                    'student_name' => $submission['student']['full_name'] ?? 'Unknown',
                    'submitted_at' => $submission['last_submitted_at'] ?? $submission['submitted_at'],
                    'late_by' => $submission['late_by'],
                ];
            })
            ->values()
            ->all();

        $totalExpected = $expectedStudents->count();
        $totalSubmitted = $submissions->count();
        $totalGraded = $submissions->filter(function ($submission) {
            return $submission['status'] === AssignmentSubmission::STATUS_GRADED
                || $submission['score'] !== null;
        })->count();
        $totalMissing = count($missingStudents);
        $totalLate = count($lateSubmissions);

        return Inertia::render('Teacher/Assignments/Submissions', [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'due_date' => $assignment->due_date?->format('Y-m-d'),
                'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                'max_score' => $assignment->max_score,
                'subject' => [
                    'id' => $assignment->subject->id,
                    'subject_code' => $assignment->subject->subject_code,
                    'title' => $assignment->subject->title,
                ],
                'due_at' => $dueAt?->toIso8601String(),
            ],
            'submissions' => $submissions,
            'missingStudents' => $missingStudents,
            'lateSubmissions' => $lateSubmissions,
            'reporting' => [
                'expected_students' => $totalExpected,
                'submitted' => $totalSubmitted,
                'graded' => $totalGraded,
                'missing' => $totalMissing,
                'late' => $totalLate,
                'submitted_percent' => $totalExpected > 0
                    ? min(100, round(($totalSubmitted / $totalExpected) * 100))
                    : 0,
                'graded_percent' => $totalExpected > 0
                    ? min(100, round(($totalGraded / $totalExpected) * 100))
                    : 0,
            ],
            'gradingTemplates' => [
                'comment_templates' => [
                    'Great structure and clarity. Keep this standard.',
                    'Good attempt. Add more supporting evidence and references.',
                    'Reasonable work, but key concepts are incomplete.',
                    'Please revise and resubmit after addressing feedback points.',
                ],
                'rubric_criteria' => [
                    ['criterion' => 'Understanding of concepts', 'max_score' => 25],
                    ['criterion' => 'Application and analysis', 'max_score' => 25],
                    ['criterion' => 'Organization and structure', 'max_score' => 25],
                    ['criterion' => 'Technical accuracy and presentation', 'max_score' => 25],
                ],
            ],
        ]);
    }

    /**
     * Download all submission files for an assignment as a ZIP archive.
     */
    public function downloadAllSubmissions(Assignment $assignment)
    {
        $user = Auth::user();

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only download submissions for your own assignments.');
        }

        if (! class_exists(ZipArchive::class)) {
            return back()->with('error', 'ZIP export is not available on this server.');
        }

        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->with('student:id,student_no,full_name')
            ->orderBy('created_at')
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('info', 'No submissions found for this assignment.');
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'assignment_zip_');
        if ($tmpPath === false) {
            return back()->with('error', 'Failed to initialize ZIP export.');
        }

        $zipPath = $tmpPath.'.zip';
        @unlink($tmpPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Failed to create ZIP archive.');
        }

        $added = 0;
        foreach ($submissions as $submission) {
            if (! Storage::disk('public')->exists($submission->file_path)) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($submission->file_path);
            $studentNo = (string) ($submission->student?->student_no ?? 'unknown');
            $studentName = Str::slug((string) ($submission->student?->full_name ?? 'student'));
            $safeOriginalName = Str::slug(pathinfo($submission->original_filename, PATHINFO_FILENAME));
            $extension = pathinfo($submission->original_filename, PATHINFO_EXTENSION);
            $safeOriginalName = $safeOriginalName !== '' ? $safeOriginalName : 'submission';

            $fileName = sprintf(
                '%s_%s_submission_%d_%s%s',
                $studentNo,
                $studentName !== '' ? $studentName : 'student',
                $submission->id,
                $safeOriginalName,
                $extension !== '' ? '.'.$extension : ''
            );

            $zip->addFile($absolutePath, $fileName);
            $added++;
        }

        $zip->close();

        if ($added === 0) {
            @unlink($zipPath);

            return back()->with('error', 'No submission files could be added to ZIP.');
        }

        $subjectCode = Str::slug((string) ($assignment->subject?->subject_code ?? 'subject'));
        $timestamp = now()->format('Ymd_His');
        $downloadName = "assignment_submissions_{$subjectCode}_{$assignment->id}_{$timestamp}.zip";

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Grade a submission.
     */
    public function grade(GradeSubmissionRequest $request, AssignmentSubmission $submission): RedirectResponse
    {
        $user = Auth::user();

        if ($submission->assignment->created_by !== $user->id) {
            abort(403, 'You can only grade submissions for your own assignments.');
        }

        $data = $request->validated();

        // Ensure score doesn't exceed max_score
        $maxScore = $submission->assignment->max_score;
        if ($data['score'] > $maxScore) {
            return back()->withErrors(['score' => "Score cannot exceed maximum score of {$maxScore}."]);
        }

        $feedback = trim((string) ($data['feedback'] ?? ''));
        $rubricFeedback = $this->formatRubricFeedback($data['rubric'] ?? []);
        if ($rubricFeedback !== '') {
            $feedback = trim($feedback !== '' ? $feedback."\n\n".$rubricFeedback : $rubricFeedback);
        }

        $submission->update([
            'score' => $data['score'],
            'feedback' => $feedback !== '' ? $feedback : null,
            'graded_by' => $user->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        $studentRecipient = $submission->student?->user;
        if ($studentRecipient) {
            $studentRecipient->notify(new AssignmentSubmissionGraded($submission));
        }

        return back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Download a student's submission file (for teacher review).
     */
    public function downloadSubmission(AssignmentSubmission $submission): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();

        if ($submission->assignment->created_by !== $user->id) {
            abort(403, 'You can only download submissions for your own assignments.');
        }

        if (! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->original_filename
        );
    }

    private function notifyEnrolledStudents(Assignment $assignment, string $action): void
    {
        $students = $assignment->course
            ?->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->with('user')
            ->get();

        if (! $students) {
            return;
        }

        $students->pluck('user')
            ->filter()
            ->each(function ($recipient) use ($assignment, $action): void {
                $recipient->notify(new AssignmentUpdated($assignment, $action));
            });
    }

    private function assignmentDueAt(Assignment $assignment): ?\Illuminate\Support\Carbon
    {
        if (! $assignment->due_date) {
            return null;
        }

        $dueAt = $assignment->due_date->copy()->endOfDay();

        if ($assignment->due_time) {
            $parts = array_map('intval', explode(':', (string) $assignment->due_time));
            $dueAt->setTime($parts[0] ?? 23, $parts[1] ?? 59, $parts[2] ?? 0);
        }

        return $dueAt;
    }

    private function formatLateDuration(int $lateByMinutes): string
    {
        $minutes = max($lateByMinutes, 0);

        if ($minutes < 60) {
            return sprintf('%d min', $minutes);
        }

        if ($minutes < 1440) {
            $hours = intdiv($minutes, 60);
            $remainingMinutes = $minutes % 60;

            return $remainingMinutes > 0
                ? sprintf('%dh %dm', $hours, $remainingMinutes)
                : sprintf('%dh', $hours);
        }

        $days = intdiv($minutes, 1440);
        $remainingHours = intdiv($minutes % 1440, 60);

        return $remainingHours > 0
            ? sprintf('%dd %dh', $days, $remainingHours)
            : sprintf('%dd', $days);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rubricRows
     */
    private function formatRubricFeedback(array $rubricRows): string
    {
        $rows = collect($rubricRows)
            ->map(function ($row) {
                $criterion = trim((string) ($row['criterion'] ?? ''));
                $score = $row['score'] ?? null;
                $maxScore = $row['max_score'] ?? null;
                $comment = trim((string) ($row['comment'] ?? ''));

                $hasScore = $score !== null && $score !== '' && is_numeric($score);
                $hasMax = $maxScore !== null && $maxScore !== '' && is_numeric($maxScore);
                if ($criterion === '' && ! $hasScore && $comment === '') {
                    return null;
                }

                $scorePart = '';
                if ($hasScore && $hasMax) {
                    $scorePart = sprintf(' (%s/%s)', (float) $score, (float) $maxScore);
                } elseif ($hasScore) {
                    $scorePart = sprintf(' (%s)', (float) $score);
                }

                return trim(sprintf(
                    '- %s%s%s',
                    $criterion !== '' ? $criterion : 'Criterion',
                    $scorePart,
                    $comment !== '' ? ': '.$comment : ''
                ));
            })
            ->filter()
            ->values();

        if ($rows->isEmpty()) {
            return '';
        }

        return "Rubric feedback:\n".$rows->implode("\n");
    }
}
