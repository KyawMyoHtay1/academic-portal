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

        // Get enrollment status for each course
        $enrollmentStatuses = [];
        if ($student) {
            $enrollments = $student->courses()->get();
            foreach ($enrollments as $enrollment) {
                $enrollmentStatuses[$enrollment->id] = $enrollment->pivot->status;
            }
        }

        // Add enrollment status to each course
        $courses = $courses->map(function ($course) use ($enrollmentStatuses) {
            $status = $enrollmentStatuses[$course->id] ?? null;
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'credits' => $course->credits,
                'semester' => $course->semester,
                'enrollment_status' => $status,
                'is_enrolled' => $status === 'approved',
            ];
        });

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'hasStudentRecord' => $student !== null,
        ]);
    }
}

