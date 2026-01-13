<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CourseRegistrationController extends Controller
{
    /**
     * Request enrollment in a course (creates pending enrollment).
     * Enrollment requires admin approval.
     */
    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return Redirect::route('courses.index')
                ->with('error', 'Student record not found. Please contact administration.');
        }

        // Check if already enrolled or has pending enrollment
        $existingEnrollment = $student->courses()
            ->where('course_id', $course->id)
            ->first();
        
        if ($existingEnrollment) {
            $status = $existingEnrollment->pivot->status;
            if ($status === 'approved') {
                return Redirect::route('courses.index')
                    ->with('error', 'You are already enrolled in this course.');
            } elseif ($status === 'pending') {
                return Redirect::route('courses.index')
                    ->with('error', 'You already have a pending enrollment request for this course.');
            } elseif ($status === 'rejected') {
                // Allow re-application after rejection
                $student->courses()->updateExistingPivot($course->id, ['status' => 'pending']);
                return Redirect::route('courses.index')
                    ->with('success', "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval.");
            }
        }

        // Create pending enrollment
        $student->courses()->attach($course->id, ['status' => 'pending']);

        return Redirect::route('courses.index')
            ->with('success', "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval.");
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

        // Check if enrolled and approved
        $enrollment = $student->courses()
            ->where('course_id', $course->id)
            ->first();
        
        if (!$enrollment || $enrollment->pivot->status !== 'approved') {
            return Redirect::route('my-courses.index')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Unenrol the student
        $student->courses()->detach($course->id);

        return Redirect::route('my-courses.index')
            ->with('success', "Successfully unenrolled from {$course->course_code} - {$course->title}.");
    }
}

