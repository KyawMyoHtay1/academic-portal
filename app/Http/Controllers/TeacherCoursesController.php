<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherCoursesController extends Controller
{
    /**
     * Display the subjects assigned to the current teacher, grouped by course.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get subjects assigned to this teacher
        $subjects = $user->teachingSubjects()
            ->with('course')
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.credits',
                'subjects.course_id',
            ]);

        // Group subjects by course
        $courses = $subjects->groupBy('course_id')->map(function ($courseSubjects, $courseId) {
            $firstSubject = $courseSubjects->first();
            $course = $firstSubject->course;
            
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'course_title' => $course->title,
                'subjects' => $courseSubjects->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'subject_code' => $subject->subject_code,
                        'title' => $subject->title,
                        'credits' => $subject->credits,
                    ];
                })->values(),
            ];
        })->values();

        return Inertia::render('Teacher/MyCourses', [
            'courses' => $courses,
        ]);
    }
}
