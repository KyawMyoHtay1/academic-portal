<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();

        // Staff/admin view: global stats
        if ($user?->isStaff()) {
            $studentCount = Student::count();
            $courseCount = Course::count();
            $feeTotal = Fee::sum('amount');

            $attendanceTotal = Attendance::count();
            $attendancePresent = Attendance::where('status', 'present')->count();
            $attendanceRate = $attendanceTotal > 0
                ? round(($attendancePresent / $attendanceTotal) * 100, 1)
                : 0;

            // Check alert system status
            $queueConnection = config('queue.default', 'sync');
            $queueConfigured = $queueConnection !== 'sync';
            
            // Check for pending jobs (if using database queue)
            $pendingJobs = 0;
            if ($queueConnection === 'database') {
                try {
                    $pendingJobs = DB::table('jobs')->count();
                } catch (\Exception $e) {
                    // Jobs table might not exist
                }
            }

            // Check scheduler status (we can't detect if it's running, but we can show if configured)
            $schedulerConfigured = true; // We have it configured in bootstrap/app.php
            
            // Determine overall status
            $alertSystemStatus = [
                'queueConfigured' => $queueConfigured,
                'queueConnection' => $queueConnection,
                'pendingJobs' => $pendingJobs,
                'schedulerConfigured' => $schedulerConfigured,
                'status' => $queueConfigured && $schedulerConfigured ? 'ready' : 'warning',
                'message' => $queueConfigured 
                    ? ($pendingJobs > 10 ? 'Queue worker may need attention (many pending jobs)' : 'System ready for automatic alerts')
                    : 'Queue is set to sync mode - configure QUEUE_CONNECTION for automatic alerts',
            ];

            // Additional stats for staff dashboard
            $pendingEnrollments = DB::table('course_student')
                ->where('status', 'pending')
                ->count();
            $pendingWithdrawals = DB::table('course_student')
                ->where('status', 'withdrawal_pending')
                ->count();
            $pendingGrades = Grade::where('status', 'pending')->count();
            $pendingPayments = Fee::where('status', 'payment_pending')->count();
            $paidFees = Fee::where('status', 'paid')->sum('amount');
            $pendingFees = Fee::where('status', 'pending')->sum('amount');

            return Inertia::render('Dashboard', [
                'role' => 'staff',
                'stats' => [
                    'students' => $studentCount,
                    'courses' => $courseCount,
                    'feeTotal' => $feeTotal,
                    'attendanceRate' => $attendanceRate,
                    'pendingEnrollments' => $pendingEnrollments,
                    'pendingWithdrawals' => $pendingWithdrawals,
                    'pendingGrades' => $pendingGrades,
                    'pendingPayments' => $pendingPayments,
                    'paidFees' => $paidFees,
                    'pendingFees' => $pendingFees,
                ],
                'alertSystemStatus' => $alertSystemStatus,
            ]);
        }

        // Teacher view: teaching-focused stats
        if ($user?->isTeacher()) {
            $subjectIds = $user->teachingSubjects()->pluck('subjects.id');
            $teachingSubjects = $subjectIds->count();

            // Get unique students from courses that contain the assigned subjects
            $studentsTaught = Student::whereHas('courses.subjects', function ($q) use ($subjectIds) {
                $q->whereIn('subjects.id', $subjectIds);
            })->distinct('students.id')->count();

            $gradesRecorded = Grade::whereIn('subject_id', $subjectIds)->count();

            $attendanceTotal = Attendance::whereIn('subject_id', $subjectIds)->count();
            $attendancePresent = Attendance::whereIn('subject_id', $subjectIds)
                ->where('status', 'present')
                ->count();
            $attendanceRate = $attendanceTotal > 0
                ? round(($attendancePresent / $attendanceTotal) * 100, 1)
                : 0;

            // Additional stats for teacher dashboard
            $pendingGrades = Grade::whereIn('subject_id', $subjectIds)
                ->where('status', 'pending')
                ->count();
            $approvedGrades = Grade::whereIn('subject_id', $subjectIds)
                ->where('status', 'approved')
                ->count();

            return Inertia::render('Dashboard', [
                'role' => 'teacher',
                'stats' => [
                    'teachingSubjects' => $teachingSubjects,
                    'studentsTaught' => $studentsTaught,
                    'gradesRecorded' => $gradesRecorded,
                    'attendanceRate' => $attendanceRate,
                    'pendingGrades' => $pendingGrades,
                    'approvedGrades' => $approvedGrades,
                ],
            ]);
        }

        // Student view: personal stats
        $student = $user?->student;
        $myCourses = $student?->courses()->count() ?? 0;
        $outstandingFees = $student?->fees()->where('status', 'pending')->sum('amount') ?? 0;
        $myGrades = $student?->grades()->count() ?? 0;

        $attendanceTotal = $student
            ? Attendance::where('student_id', $student->id)->count()
            : 0;
        $attendancePresent = $student
            ? Attendance::where('student_id', $student->id)->where('status', 'present')->count()
            : 0;
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        // Additional stats for student dashboard
        $gpa = $student?->calculateGPA() ?? null;
        $pendingEnrollments = $student
            ? DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('status', 'pending')
                ->count()
            : 0;
        $approvedEnrollments = $student
            ? DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('status', 'approved')
                ->count()
            : 0;

        return Inertia::render('Dashboard', [
            'role' => 'student',
            'stats' => [
                'myCourses' => $myCourses,
                'outstandingFees' => $outstandingFees,
                'myGrades' => $myGrades,
                'attendanceRate' => $attendanceRate,
                'gpa' => $gpa,
                'pendingEnrollments' => $pendingEnrollments,
                'approvedEnrollments' => $approvedEnrollments,
            ],
        ]);
    }
}


