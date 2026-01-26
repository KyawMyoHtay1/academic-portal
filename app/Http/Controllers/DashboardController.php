<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
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

            return Inertia::render('Dashboard', [
                'role' => 'staff',
                'stats' => [
                    'students' => $studentCount,
                    'courses' => $courseCount,
                    'feeTotal' => $feeTotal,
                    'attendanceRate' => $attendanceRate,
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

            return Inertia::render('Dashboard', [
                'role' => 'teacher',
                'stats' => [
                    'teachingSubjects' => $teachingSubjects,
                    'studentsTaught' => $studentsTaught,
                    'gradesRecorded' => $gradesRecorded,
                    'attendanceRate' => $attendanceRate,
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

        return Inertia::render('Dashboard', [
            'role' => 'student',
            'stats' => [
                'myCourses' => $myCourses,
                'outstandingFees' => $outstandingFees,
                'myGrades' => $myGrades,
                'attendanceRate' => $attendanceRate,
            ],
        ]);
    }
}


