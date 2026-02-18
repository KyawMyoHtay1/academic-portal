<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MyCoursesController extends Controller
{
    /**
     * Display the authenticated student's enrolled courses.
     * Timebox 1: Student self-view only.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return Inertia::render('MyCourses/Index', [
                'courses' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        $courses = $student->courses()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->with(['subjects' => function ($q) {
                $q->select('id', 'course_id', 'subject_code', 'title', 'photo')
                    ->orderBy('subject_code');
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.credits',
                'courses.semester',
                'courses.photo',
                'course_student.created_at as enrolled_at',
                'course_student.status as enrollment_status',
            ]);

        return Inertia::render('MyCourses/Index', [
            'courses' => $courses,
        ]);
    }
}
