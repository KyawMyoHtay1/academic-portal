<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherCoursesController extends Controller
{
    /**
     * Display the courses assigned to the current teacher.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get courses assigned to this teacher
        $courses = $user->teachingCourses()
            ->orderBy('course_code')
            ->get([
                'id',
                'course_code',
                'title',
                'credits',
                'semester',
            ]);

        return Inertia::render('Teacher/MyCourses', [
            'courses' => $courses,
        ]);
    }
}
