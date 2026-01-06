<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CourseRegistrationController extends Controller
{
    /**
     * Enrol the authenticated student in a course.
     * Timebox 1: Student self-enrollment only.
     */
    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return Redirect::route('courses.index')
                ->with('error', 'Student record not found. Please contact administration.');
        }

        // Check if already enrolled
        if ($student->courses()->where('course_id', $course->id)->exists()) {
            return Redirect::route('courses.index')
                ->with('error', 'You are already enrolled in this course.');
        }

        // Enrol the student
        $student->courses()->attach($course->id);

        return Redirect::route('courses.index')
            ->with('success', "Successfully enrolled in {$course->course_code} - {$course->title}.");
    }

    /**
     * Unenrol the authenticated student from a course.
     */
    public function unenroll(Request $request, Course $course)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return Redirect::route('my-courses.index')
                ->with('error', 'Student record not found.');
        }

        // Check if enrolled
        if (!$student->courses()->where('course_id', $course->id)->exists()) {
            return Redirect::route('my-courses.index')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Unenrol the student
        $student->courses()->detach($course->id);

        return Redirect::route('my-courses.index')
            ->with('success', "Successfully unenrolled from {$course->course_code} - {$course->title}.");
    }
}

