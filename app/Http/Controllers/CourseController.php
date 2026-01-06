<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of available courses (read-only for Timebox 1).
     * Students can view courses but cannot create/edit/delete yet.
     */
    public function index(): Response
    {
        $courses = Course::orderBy('course_code')
            ->get([
                'id',
                'course_code',
                'title',
                'credits',
                'semester',
            ]);

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
        ]);
    }
}

