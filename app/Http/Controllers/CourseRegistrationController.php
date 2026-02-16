<?php

namespace App\Http\Controllers;

use App\Http\Requests\Courses\EnrollCourseRequest;
use App\Http\Requests\Courses\UnenrollCourseRequest;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CourseRegistrationController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    /**
     * Request enrollment in a course (creates pending enrollment).
     * Enrollment requires admin approval.
     */
    public function enroll(EnrollCourseRequest $request, Course $course)
    {
        $this->authorize('enroll', $course);

        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return Redirect::route('courses.index')
                ->with('error', 'Student record not found. Please contact administration.');
        }

        $result = $this->enrollmentService->requestEnrollment($student, $course);

        return Redirect::route('courses.index')
            ->with($result['level'], $result['message']);
    }

    /**
     * Request withdrawal from a course (creates withdrawal request).
     * Withdrawal requires admin approval.
     */
    public function unenroll(UnenrollCourseRequest $request, Course $course)
    {
        $this->authorize('unenroll', $course);

        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return Redirect::route('my-courses.index')
                ->with('error', 'Student record not found.');
        }

        $result = $this->enrollmentService->requestWithdrawal($student, $course);

        return Redirect::route('my-courses.index')
            ->with($result['level'], $result['message']);
    }
}
