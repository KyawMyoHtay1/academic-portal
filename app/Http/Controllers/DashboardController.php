<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
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

            return Inertia::render('Dashboard', [
                'role' => 'staff',
                'stats' => [
                    'students' => $studentCount,
                    'courses' => $courseCount,
                    'feeTotal' => $feeTotal,
                    'attendanceRate' => $attendanceRate,
                ],
            ]);
        }

        // Teacher view: teaching-focused stats
        if ($user?->isTeacher()) {
            $courseIds = $user->teachingCourses()->pluck('courses.id');
            $teachingCourses = $courseIds->count();

            $studentsTaught = Student::whereHas('courses', function ($q) use ($courseIds) {
                $q->whereIn('courses.id', $courseIds);
            })->distinct('students.id')->count();

            $gradesRecorded = Grade::whereIn('course_id', $courseIds)->count();

            $attendanceTotal = Attendance::whereIn('course_id', $courseIds)->count();
            $attendancePresent = Attendance::whereIn('course_id', $courseIds)
                ->where('status', 'present')
                ->count();
            $attendanceRate = $attendanceTotal > 0
                ? round(($attendancePresent / $attendanceTotal) * 100, 1)
                : 0;

            return Inertia::render('Dashboard', [
                'role' => 'teacher',
                'stats' => [
                    'teachingCourses' => $teachingCourses,
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


