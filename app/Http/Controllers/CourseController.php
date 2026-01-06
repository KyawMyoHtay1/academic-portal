<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of available courses with enrollment status.
     * Timebox 1: Students can view and enroll in courses.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        // Get all courses
        $courses = Course::orderBy('course_code')
            ->get([
                'id',
                'course_code',
                'title',
                'credits',
                'semester',
            ]);

        // Get enrolled course IDs for the current student
        $enrolledCourseIds = $student
            ? $student->courses()->pluck('courses.id')->toArray()
            : [];

        // Add enrollment status to each course
        $courses = $courses->map(function ($course) use ($enrolledCourseIds) {
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'credits' => $course->credits,
                'semester' => $course->semester,
                'is_enrolled' => in_array($course->id, $enrolledCourseIds),
            ];
        });

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'hasStudentRecord' => $student !== null,
        ]);
    }
}

