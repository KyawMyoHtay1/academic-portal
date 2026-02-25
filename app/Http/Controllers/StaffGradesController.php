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
use App\Services\SubjectGradeCalculator;
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
        $analytics = $this->buildSubjectAnalytics($subject);

        return Inertia::render('Admin/Grades/Show', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'rows' => $studentRows,
            'analytics' => $analytics,
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

        if ($grade->status !== Grade::STATUS_PENDING) {
            return back()->with('info', 'Grade is not pending review.');
        }

        $this->approvePendingGrade($grade);

        return back()->with('success', 'Grade approved and published.');
    }

    public function reject(RejectGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorize('review', $grade);

        $data = $request->validated();

        if ($grade->status !== Grade::STATUS_PENDING) {
            return back()->with('info', 'Grade is not pending review.');
        }

        $this->rejectPendingGrade($grade, $data['reason'] ?? null);

        return back()->with('success', 'Grade rejected.');
    }

    public function bulkReview(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Grade::class);

        $data = $request->validate([
            'grade_ids' => ['required', 'array', 'min:1'],
            'grade_ids.*' => ['integer', 'distinct', 'exists:grades,id'],
            'action' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'string', 'max:255', 'required_if:action,reject'],
        ]);

        $reason = trim((string) ($data['reason'] ?? ''));
        if ($data['action'] === 'reject' && $reason === '') {
            return back()->withErrors(['reason' => 'A rejection reason is required for bulk reject.']);
        }

        $grades = Grade::query()
            ->whereIn('id', $data['grade_ids'])
            ->with([
                'student.user:id,name,email',
                'grader:id,name,email',
                'subject:id,subject_code,title',
            ])
            ->get();

        $processed = 0;
        $skipped = 0;

        foreach ($grades as $grade) {
            $this->authorize('review', $grade);

            if ($grade->status !== Grade::STATUS_PENDING) {
                $skipped++;
                continue;
            }

            if ($data['action'] === 'approve') {
                $this->approvePendingGrade($grade);
            } else {
                $this->rejectPendingGrade($grade, $reason);
            }

            $processed++;
        }

        if ($processed === 0) {
            return back()->with('info', 'No pending grades were processed.');
        }

        $actionLabel = $data['action'] === 'approve' ? 'approved' : 'rejected';
        $message = ucfirst($actionLabel)." {$processed} grade(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} non-pending grade(s).";
        }

        return back()->with('success', $message);
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
        $studentIds = $students->pluck('id')->all();

        $calculator = new SubjectGradeCalculator();
        $assignmentDataByStudent = $calculator->calculateForSubjectStudents($subject->id, $studentIds);

        $grades = Grade::query()
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $studentIds)
            ->whereIn('status', [
                Grade::STATUS_PENDING,
                Grade::STATUS_APPROVED,
                Grade::STATUS_REJECTED,
            ])
            ->with(['grader:id,name', 'reviewer:id,name'])
            ->get()
            ->keyBy('student_id');

        return $students->map(function ($student) use ($grades, $assignmentDataByStudent) {
            $grade = $grades->get($student->id);
            $letterGrade = $grade?->letter_grade;
            $assignmentData = $assignmentDataByStudent[$student->id] ?? [
                'computed_grade' => null,
                'breakdown' => [],
                'total_assignments' => 0,
                'graded_assignments' => 0,
                'ungraded_assignments' => 0,
                'has_assignments' => false,
            ];

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
                'assignment' => [
                    'computed_grade' => $assignmentData['computed_grade'],
                    'breakdown' => $assignmentData['breakdown'],
                    'total_assignments' => $assignmentData['total_assignments'],
                    'graded_assignments' => $assignmentData['graded_assignments'],
                    'ungraded_assignments' => $assignmentData['ungraded_assignments'],
                    'has_assignments' => $assignmentData['has_assignments'],
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSubjectAnalytics(Subject $subject): array
    {
        $enrolledStudentIds = $subject->course->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->pluck('students.id');

        $grades = Grade::query()
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $enrolledStudentIds)
            ->get([
                'id',
                'student_id',
                'score',
                'status',
                'updated_at',
            ]);

        $totalStudents = $enrolledStudentIds->count();
        $gradedStudentCount = $grades->pluck('student_id')->unique()->count();
        $scoreValues = $grades
            ->pluck('score')
            ->filter(fn ($value) => $value !== null)
            ->map(fn ($value) => (float) $value)
            ->values();
        $scoredCount = $scoreValues->count();

        $letterDistribution = [
            'A' => 0,
            'B' => 0,
            'C' => 0,
            'D' => 0,
            'E' => 0,
            'F' => 0,
        ];
        foreach ($scoreValues as $score) {
            if ($score >= 80) {
                $letterDistribution['A']++;
            } elseif ($score >= 70) {
                $letterDistribution['B']++;
            } elseif ($score >= 60) {
                $letterDistribution['C']++;
            } elseif ($score >= 50) {
                $letterDistribution['D']++;
            } elseif ($score >= 40) {
                $letterDistribution['E']++;
            } else {
                $letterDistribution['F']++;
            }
        }

        $monthKeys = collect(range(5, 0))
            ->map(fn ($offset) => now()->copy()->startOfMonth()->subMonths($offset)->format('Y-m'))
            ->push(now()->copy()->startOfMonth()->format('Y-m'))
            ->values();

        $trendMap = $monthKeys->mapWithKeys(function ($monthKey) {
            return [$monthKey => [
                'month_key' => $monthKey,
                'label' => date('M Y', strtotime($monthKey.'-01')),
                'count' => 0,
                'score_sum' => 0.0,
                'score_count' => 0,
            ]];
        });

        foreach ($grades as $grade) {
            $monthKey = $grade->updated_at?->format('Y-m');
            if (! $monthKey || ! $trendMap->has($monthKey)) {
                continue;
            }

            $current = $trendMap->get($monthKey);
            $current['count']++;
            if ($grade->score !== null) {
                $current['score_sum'] += (float) $grade->score;
                $current['score_count']++;
            }
            $trendMap->put($monthKey, $current);
        }

        $statusCounts = [
            Grade::STATUS_DRAFT => (int) $grades->where('status', Grade::STATUS_DRAFT)->count(),
            Grade::STATUS_PENDING => (int) $grades->where('status', Grade::STATUS_PENDING)->count(),
            Grade::STATUS_APPROVED => (int) $grades->where('status', Grade::STATUS_APPROVED)->count(),
            Grade::STATUS_REJECTED => (int) $grades->where('status', Grade::STATUS_REJECTED)->count(),
        ];

        $passCount = (int) $scoreValues->filter(fn ($score) => $score >= 40)->count();
        $passRate = $scoredCount > 0
            ? round(($passCount / $scoredCount) * 100, 1)
            : 0.0;

        return [
            'status_counts' => $statusCounts,
            'summary' => [
                'total_students' => $totalStudents,
                'graded_students' => $gradedStudentCount,
                'ungraded_students' => max($totalStudents - $gradedStudentCount, 0),
                'average_score' => $scoredCount > 0 ? round($scoreValues->avg(), 2) : null,
                'highest_score' => $scoredCount > 0 ? round($scoreValues->max(), 2) : null,
                'lowest_score' => $scoredCount > 0 ? round($scoreValues->min(), 2) : null,
                'pass_rate' => $passRate,
                'scored_count' => $scoredCount,
            ],
            'distribution' => $letterDistribution,
            'trend' => $trendMap
                ->values()
                ->map(function (array $entry) {
                    return [
                        'month_key' => $entry['month_key'],
                        'label' => $entry['label'],
                        'count' => $entry['count'],
                        'average_score' => $entry['score_count'] > 0
                            ? round($entry['score_sum'] / $entry['score_count'], 2)
                            : null,
                    ];
                })
                ->all(),
        ];
    }

    private function approvePendingGrade(Grade $grade): void
    {
        $grade->loadMissing([
            'student.user:id,name,email',
            'grader:id,name,email',
            'subject:id,subject_code,title',
        ]);

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

        $student = Student::find($grade->student_id);
        if ($student && $student->user) {
            $student->user->notify(new GradePublished($grade));
        }
        if ($grade->grader && $grade->grader->id !== Auth::id()) {
            $grade->grader->notify(new GradeReviewOutcome($grade, 'approved'));
        }
    }

    private function rejectPendingGrade(Grade $grade, ?string $reason = null): void
    {
        $grade->loadMissing([
            'student.user:id,name,email',
            'grader:id,name,email',
            'subject:id,subject_code,title',
        ]);

        $grade->reject(Auth::id(), $reason);

        GradeReviewLog::create([
            'grade_id' => $grade->id,
            'performed_by' => Auth::id(),
            'action' => 'rejected',
            'reason' => $reason,
        ]);

        Log::info('grade.review_decision', [
            'grade_id' => $grade->id,
            'student_id' => $grade->student_id,
            'subject_id' => $grade->subject_id,
            'decision' => 'rejected',
            'reason' => $reason,
            'reviewed_by' => Auth::id(),
        ]);
        if ($grade->grader && $grade->grader->id !== Auth::id()) {
            $grade->grader->notify(new GradeReviewOutcome($grade, 'rejected', $reason));
        }
    }
}
