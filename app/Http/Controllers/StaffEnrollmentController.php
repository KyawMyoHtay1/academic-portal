<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StaffEnrollmentController extends Controller
{
    /**
     * Display a listing of pending enrollments and withdrawal requests.
     */
    public function index(): Response
    {
        $pendingEnrollments = DB::table('course_student')
            ->where('status', 'pending')
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
            ->where('status', 'withdrawal_pending')
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
    public function approve(Request $request, $enrollment): RedirectResponse
    {
        $enrollmentRecord = DB::table('course_student')
            ->where('id', $enrollment)
            ->where('status', 'pending')
            ->first();

        if (!$enrollmentRecord) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Enrollment not found or already processed.');
        }

        DB::table('course_student')
            ->where('id', $enrollment)
            ->update(['status' => 'approved']);

        $course = Course::find($enrollmentRecord->course_id);
        $student = Student::find($enrollmentRecord->student_id);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', "Enrollment approved for {$student->full_name} in {$course->course_code}.");
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject(Request $request, $enrollment): RedirectResponse
    {
        $enrollmentRecord = DB::table('course_student')
            ->where('id', $enrollment)
            ->where('status', 'pending')
            ->first();

        if (!$enrollmentRecord) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Enrollment not found or already processed.');
        }

        DB::table('course_student')
            ->where('id', $enrollment)
            ->update(['status' => 'rejected']);

        $course = Course::find($enrollmentRecord->course_id);
        $student = Student::find($enrollmentRecord->student_id);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', "Enrollment rejected for {$student->full_name} in {$course->course_code}.");
    }

    /**
     * Approve a withdrawal request (removes the enrollment).
     */
    public function approveWithdrawal(Request $request, $enrollment): RedirectResponse
    {
        $enrollmentRecord = DB::table('course_student')
            ->where('id', $enrollment)
            ->where('status', 'withdrawal_pending')
            ->first();

        if (!$enrollmentRecord) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Withdrawal request not found or already processed.');
        }

        $course = Course::find($enrollmentRecord->course_id);
        $student = Student::find($enrollmentRecord->student_id);

        // Remove the enrollment
        DB::table('course_student')
            ->where('id', $enrollment)
            ->delete();

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', "Withdrawal approved for {$student->full_name} from {$course->course_code}.");
    }

    /**
     * Reject a withdrawal request (keeps the enrollment as approved).
     */
    public function rejectWithdrawal(Request $request, $enrollment): RedirectResponse
    {
        $enrollmentRecord = DB::table('course_student')
            ->where('id', $enrollment)
            ->where('status', 'withdrawal_pending')
            ->first();

        if (!$enrollmentRecord) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Withdrawal request not found or already processed.');
        }

        // Revert to approved status
        DB::table('course_student')
            ->where('id', $enrollment)
            ->update(['status' => 'approved']);

        $course = Course::find($enrollmentRecord->course_id);
        $student = Student::find($enrollmentRecord->student_id);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', "Withdrawal rejected for {$student->full_name} from {$course->course_code}. Student remains enrolled.");
    }
}
