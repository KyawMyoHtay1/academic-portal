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

        if ($assignment->created_by !== $user->id) {
            abort(403, 'You can only view submissions for your own assignments.');
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
                'due_date' => $assignment->due_date?->format('Y-m-d'),
                'due_time' => $assignment->due_time ? (is_string($assignment->due_time) ? substr($assignment->due_time, 0, 5) : $assignment->due_time->format('H:i')) : null,
                'max_score' => $assignment->max_score,
                'subject' => [
                    'id' => $assignment->subject->id,
                    'subject_code' => $assignment->subject->subject_code,
                    'title' => $assignment->subject->title,
                ],
            ],
            'submissions' => $submissions,
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
