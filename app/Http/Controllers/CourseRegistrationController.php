<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Use transaction to prevent race conditions
        return DB::transaction(function () use ($student, $course) {
            // Check if already enrolled using direct DB query for reliability
            $existingEnrollment = DB::table('course_student')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();
            
            if ($existingEnrollment) {
                $status = $existingEnrollment->status;
                if ($status === 'approved') {
                    return Redirect::route('courses.index')
                        ->with('error', 'You are already enrolled in this course.');
                } elseif ($status === 'pending') {
                    return Redirect::route('courses.index')
                        ->with('error', 'You already have a pending enrollment request for this course.');
                } elseif ($status === 'withdrawal_pending') {
                    return Redirect::route('courses.index')
                        ->with('error', "You have a pending withdrawal request for {$course->course_code} - {$course->title}. Please wait for admin approval before registering again.");
                } elseif ($status === 'rejected') {
                    // Allow re-application after rejection
                    DB::table('course_student')
                        ->where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->update(['status' => 'pending', 'updated_at' => now()]);
                    return Redirect::route('courses.index')
                        ->with('success', "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval.");
                }
            }

            // Create pending enrollment with error handling
            try {
                DB::table('course_student')->insert([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle duplicate entry error gracefully
                if ($e->getCode() == 23000) {
                    // Re-check what the actual status is
                    $existingEnrollment = DB::table('course_student')
                        ->where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->first();
                    
                    if ($existingEnrollment) {
                        $status = $existingEnrollment->status;
                        if ($status === 'approved') {
                            return Redirect::route('courses.index')
                                ->with('error', 'You are already enrolled in this course.');
                        } elseif ($status === 'pending') {
                            return Redirect::route('courses.index')
                                ->with('error', 'You already have a pending enrollment request for this course.');
                        } elseif ($status === 'withdrawal_pending') {
                            return Redirect::route('courses.index')
                                ->with('error', "You have a pending withdrawal request for {$course->course_code} - {$course->title}. Please wait for admin approval before registering again.");
                        }
                    }
                    
                    return Redirect::route('courses.index')
                        ->with('error', 'An error occurred while processing your enrollment request. Please try again.');
                }
                throw $e;
            }

            return Redirect::route('courses.index')
                ->with('success', "Enrollment request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval.");
        });
    }

    /**
     * Request withdrawal from a course (creates withdrawal request).
     * Withdrawal requires admin approval.
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
        
        if (!$enrollment) {
            return Redirect::route('my-courses.index')
                ->with('error', 'You are not enrolled in this course.');
        }

        $status = $enrollment->pivot->status;
        
        if ($status !== 'approved') {
            if ($status === 'withdrawal_pending') {
                return Redirect::route('my-courses.index')
                    ->with('error', 'You already have a pending withdrawal request for this course.');
            }
            return Redirect::route('my-courses.index')
                ->with('error', 'You are not enrolled in this course.');
        }

        // Create withdrawal request
        $student->courses()->updateExistingPivot($course->id, ['status' => 'withdrawal_pending']);

        return Redirect::route('my-courses.index')
            ->with('success', "Withdrawal request submitted for {$course->course_code} - {$course->title}. Waiting for admin approval.");
    }
}

