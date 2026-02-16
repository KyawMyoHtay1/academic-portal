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
     * Display a listing of pending enrollments and withdrawal requests.
     */
    public function index(): Response
    {
        $pendingEnrollments = DB::table('course_student')
            ->where('course_student.status', 'pending')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->select(
                'course_student.id as enrollment_id',
                'course_student.created_at as requested_at',
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
            )
            ->orderBy('course_student.created_at', 'desc')
            ->get();

        $pendingWithdrawals = DB::table('course_student')
            ->where('course_student.status', 'withdrawal_pending')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->select(
                'course_student.id as enrollment_id',
                'course_student.updated_at as requested_at',
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
            )
            ->orderBy('course_student.updated_at', 'desc')
            ->get();

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $pendingEnrollments,
            'withdrawals' => $pendingWithdrawals,
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
