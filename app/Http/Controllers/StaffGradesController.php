<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Grades\ApproveGradeRequest;
use App\Http\Requests\Staff\Grades\RejectGradeRequest;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\GradePublished;
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

        $students = $subject->course->students()
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
            ->with(['grader:id,name', 'reviewer:id,name'])
            ->get()
            ->keyBy('student_id');

        $studentRows = $students->map(function ($student) use ($grades) {
            $grade = $grades->get($student->id);

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
                    'status' => $grade->status,
                    'graded_by' => $grade->grader?->name,
                    'submitted_at' => $grade->updated_at?->toDateTimeString(),
                    'reviewed_by' => $grade->reviewer?->name,
                    'reviewed_at' => $grade->reviewed_at?->toDateTimeString(),
                    'rejection_reason' => $grade->rejection_reason,
                ] : null,
            ];
        });

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

    public function approve(ApproveGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorize('review', $grade);

        $request->validated();

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

        return back()->with('success', 'Grade approved and published.');
    }

    public function reject(RejectGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorize('review', $grade);

        $data = $request->validated();

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

        return back()->with('success', 'Grade rejected.');
    }
}
