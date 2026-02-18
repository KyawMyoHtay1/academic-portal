<?php

namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StaffEnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    /**
     * Display and filter course enrollments for staff.
     */
    public function index(): Response
    {
        $filters = request()->only(['status', 'search']);
        $status = $filters['status'] ?? 'pending';
        $search = trim((string) ($filters['search'] ?? ''));

        // Base query for enrollments with student and course details
        $query = DB::table('course_student')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->select(
                'course_student.id as enrollment_id',
                'course_student.created_at',
                'course_student.updated_at',
                'course_student.status',
                'courses.id as course_id',
                'courses.course_code',
                'courses.title as course_title',
                'courses.credits',
                'courses.semester',
                'courses.photo as course_photo',
                'students.id as student_id',
                'students.student_no',
                'students.full_name as student_name',
                'students.email as student_email',
                'students.programme',
                'students.photo as student_photo'
            );

        // Apply search (student name/number/email, course code/title, programme)
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $like = '%'.$search.'%';
                $q->where('students.full_name', 'like', $like)
                    ->orWhere('students.student_no', 'like', $like)
                    ->orWhere('students.email', 'like', $like)
                    ->orWhere('students.programme', 'like', $like)
                    ->orWhere('courses.course_code', 'like', $like)
                    ->orWhere('courses.title', 'like', $like);
            });
        }

        // Status filter: all | pending | approved | rejected | withdrawal_pending
        if ($status !== 'all') {
            $query->where('course_student.status', $status);
        }

        // Order by most recent request/update
        $query->orderByDesc('course_student.updated_at')
            ->orderByDesc('course_student.created_at');

        $perPage = 15;
        $enrollments = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($row) {
                return [
                    'enrollment_id' => $row->enrollment_id,
                    'status' => $row->status,
                    'requested_at' => $row->updated_at ?? $row->created_at,
                    'course_id' => $row->course_id,
                    'course_code' => $row->course_code,
                    'course_title' => $row->course_title,
                    'credits' => $row->credits,
                    'semester' => $row->semester,
                    'course_photo' => $row->course_photo,
                    'student_id' => $row->student_id,
                    'student_no' => $row->student_no,
                    'student_name' => $row->student_name,
                    'student_email' => $row->student_email,
                    'programme' => $row->programme,
                    'student_photo' => $row->student_photo,
                ];
            });

        // Status counts for tabs
        $statusCountsRaw = DB::table('course_student')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'pending' => (int) ($statusCountsRaw['pending'] ?? 0),
            'approved' => (int) ($statusCountsRaw['approved'] ?? 0),
            'rejected' => (int) ($statusCountsRaw['rejected'] ?? 0),
            'withdrawal_pending' => (int) ($statusCountsRaw['withdrawal_pending'] ?? 0),
        ];

        $statusCounts['all'] = array_sum($statusCounts);

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
            'counts' => $statusCounts,
        ]);
    }

    /**
     * Approve a pending enrollment.
     */
    public function approve($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->approveEnrollment($enrollment);

        return redirect()
            ->route('admin.enrollments.index')
            ->with($result['level'], $result['message']);
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->rejectEnrollment($enrollment);

        return redirect()
            ->route('admin.enrollments.index')
            ->with($result['level'], $result['message']);
    }

    /**
     * Approve a withdrawal request (removes the enrollment).
     */
    public function approveWithdrawal($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->approveWithdrawal($enrollment);

        return redirect()
            ->route('admin.enrollments.index')
            ->with($result['level'], $result['message']);
    }

    /**
     * Reject a withdrawal request (keeps the enrollment as approved).
     */
    public function rejectWithdrawal($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->rejectWithdrawal($enrollment);

        return redirect()
            ->route('admin.enrollments.index')
            ->with($result['level'], $result['message']);
    }
}
