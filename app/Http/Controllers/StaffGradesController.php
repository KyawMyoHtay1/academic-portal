<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Grades\ApproveGradeRequest;
use App\Http\Requests\Staff\Grades\RejectGradeRequest;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\GradePublished;
use App\Notifications\GradeReviewOutcome;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class StaffGradesController extends Controller
{
    /**
     * List subjects with grades pending review.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Grade::class);

        $subjects = Subject::query()
            ->whereHas('grades', function ($q) {
                $q->where('status', Grade::STATUS_PENDING);
            })
            ->with(['course:id,course_code,title', 'grades' => function ($q) {
                $q->where('status', Grade::STATUS_PENDING);
            }])
            ->orderBy('subject_code')
            ->get(['id', 'course_id', 'subject_code', 'title', 'photo'])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'photo' => $subject->photo,
                    'course_code' => $subject->course?->course_code,
                    'course_title' => $subject->course?->title,
                    'pending_count' => $subject->grades?->count() ?? 0,
                ];
            });

        return Inertia::render('Admin/Grades/Index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show pending grades for a subject.
     */
    public function show(Subject $subject): Response
    {
        $this->authorize('viewAny', Grade::class);

        $studentRows = $this->buildSubjectRows($subject);

        return Inertia::render('Admin/Grades/Show', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'rows' => $studentRows,
        ]);
    }

    /**
     * Export grade sheet for a subject.
     */
    public function export(Subject $subject, Request $request, string $format)
    {
        $this->authorize('viewAny', Grade::class);

        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $status = trim((string) $request->input('status', 'all'));
        $search = trim((string) $request->input('search', ''));
        $allowedStatuses = ['pending', 'approved', 'rejected', 'all'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $rows = $this->buildSubjectRows($subject)
            ->filter(function (array $row) use ($status, $search) {
                $grade = $row['grade'];
                if (! $grade) {
                    return false;
                }

                if ($status !== 'all' && ($grade['status'] ?? null) !== $status) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                $needle = strtolower($search);
                $haystack = strtolower(sprintf(
                    '%s %s',
                    (string) ($row['student']['student_no'] ?? ''),
                    (string) ($row['student']['full_name'] ?? '')
                ));

                return str_contains($haystack, $needle);
            })
            ->values();

        $timestamp = now()->format('Ymd_His');
        $safeSubjectCode = preg_replace('/[^A-Za-z0-9_-]/', '-', $subject->subject_code);
        $safeSubjectCode = $safeSubjectCode !== '' ? $safeSubjectCode : 'subject';

        if ($format === 'csv') {
            $filename = "grade_sheet_{$safeSubjectCode}_{$timestamp}.csv";

            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Student No',
                    'Student Name',
                    'Score',
                    'Letter Grade',
                    'Status',
                    'Graded By',
                    'Submitted At',
                    'Reviewed By',
                    'Reviewed At',
                    'Rejection Reason',
                ]);

                foreach ($rows as $row) {
                    $grade = $row['grade'] ?? [];
                    fputcsv($handle, [
                        $row['student']['student_no'] ?? '',
                        $row['student']['full_name'] ?? '',
                        $grade['score'] ?? '',
                        $grade['letter_grade'] ?? '',
                        $grade['status'] ?? '',
                        $grade['graded_by'] ?? '',
                        $grade['submitted_at'] ?? '',
                        $grade['reviewed_by'] ?? '',
                        $grade['reviewed_at'] ?? '',
                        $grade['rejection_reason'] ?? '',
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $pdf = Pdf::loadView('grades.subject_sheet', [
            'subject' => [
                'code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
                'semester' => $subject->course->semester,
            ],
            'rows' => $rows,
            'status' => $status,
            'search' => $search,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("grade_sheet_{$safeSubjectCode}_{$timestamp}.pdf");
    }

    public function approve(ApproveGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorize('review', $grade);

        $request->validated();
        $grade->loadMissing([
            'student.user:id,name,email',
            'grader:id,name,email',
            'subject:id,subject_code,title',
        ]);

        if ($grade->status !== Grade::STATUS_PENDING) {
            return back()->with('info', 'Grade is not pending review.');
        }

        $grade->approve(Auth::id());

        GradeReviewLog::create([
            'grade_id' => $grade->id,
            'performed_by' => Auth::id(),
            'action' => 'approved',
        ]);

        Log::info('grade.review_decision', [
            'grade_id' => $grade->id,
            'student_id' => $grade->student_id,
            'subject_id' => $grade->subject_id,
            'decision' => 'approved',
            'reviewed_by' => Auth::id(),
        ]);

        // Notify student when grade is finalized (approved).
        $student = Student::find($grade->student_id);
        if ($student && $student->user) {
            $student->user->notify(new GradePublished($grade));
        }
        if ($grade->grader && $grade->grader->id !== Auth::id()) {
            $grade->grader->notify(new GradeReviewOutcome($grade, 'approved'));
        }

        return back()->with('success', 'Grade approved and published.');
    }

    public function reject(RejectGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorize('review', $grade);

        $data = $request->validated();
        $grade->loadMissing([
            'student.user:id,name,email',
            'grader:id,name,email',
            'subject:id,subject_code,title',
        ]);

        if ($grade->status !== Grade::STATUS_PENDING) {
            return back()->with('info', 'Grade is not pending review.');
        }

        $grade->reject(Auth::id(), $data['reason'] ?? null);

        GradeReviewLog::create([
            'grade_id' => $grade->id,
            'performed_by' => Auth::id(),
            'action' => 'rejected',
            'reason' => $data['reason'] ?? null,
        ]);

        Log::info('grade.review_decision', [
            'grade_id' => $grade->id,
            'student_id' => $grade->student_id,
            'subject_id' => $grade->subject_id,
            'decision' => 'rejected',
            'reason' => $data['reason'] ?? null,
            'reviewed_by' => Auth::id(),
        ]);
        if ($grade->grader && $grade->grader->id !== Auth::id()) {
            $grade->grader->notify(new GradeReviewOutcome($grade, 'rejected', $data['reason'] ?? null));
        }

        return back()->with('success', 'Grade rejected.');
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function buildSubjectRows(Subject $subject)
    {
        $students = $subject->course->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->orderBy('students.full_name')
            ->get([
                'students.id',
                'students.student_no',
                'students.full_name',
                'students.photo',
            ]);

        $grades = Grade::query()
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->whereIn('status', [
                Grade::STATUS_PENDING,
                Grade::STATUS_APPROVED,
                Grade::STATUS_REJECTED,
            ])
            ->with(['grader:id,name', 'reviewer:id,name'])
            ->get()
            ->keyBy('student_id');

        return $students->map(function ($student) use ($grades) {
            $grade = $grades->get($student->id);
            $letterGrade = $grade?->letter_grade;

            return [
                'student' => [
                    'id' => $student->id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->full_name,
                    'photo' => $student->photo,
                ],
                'grade' => $grade ? [
                    'id' => $grade->id,
                    'score' => $grade->score,
                    'letter_grade' => $letterGrade,
                    'status' => $grade->status,
                    'graded_by' => $grade->grader?->name,
                    'submitted_at' => $grade->updated_at?->toDateTimeString(),
                    'reviewed_by' => $grade->reviewer?->name,
                    'reviewed_at' => $grade->reviewed_at?->toDateTimeString(),
                    'rejection_reason' => $grade->rejection_reason,
                ] : null,
            ];
        });
    }
}
