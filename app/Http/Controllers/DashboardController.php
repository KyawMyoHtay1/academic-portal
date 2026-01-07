<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $studentCount = Student::count();
        $courseCount = Course::count();
        $feeTotal = Fee::sum('amount');

        $attendanceTotal = Attendance::count();
        $attendancePresent = Attendance::where('status', 'present')->count();
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : 0;

        return Inertia::render('Dashboard', [
            'stats' => [
                'students' => $studentCount,
                'courses' => $courseCount,
                'feeTotal' => $feeTotal,
                'attendanceRate' => $attendanceRate,
            ],
        ]);
    }
}


