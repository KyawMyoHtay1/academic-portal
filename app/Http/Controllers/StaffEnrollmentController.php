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
     * Display a listing of pending enrollments.
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
                'courses.id as course_id',
                'courses.course_code',
                'courses.title as course_title',
                'courses.credits',
                'courses.semester',
                'students.id as student_id',
                'students.student_no',
                'students.full_name as student_name',
                'students.email as student_email',
                'students.programme'
            )
            ->orderBy('course_student.created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $pendingEnrollments,
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
}
