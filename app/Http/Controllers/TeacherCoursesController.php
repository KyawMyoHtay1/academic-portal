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

        // Courses directly assigned to this teacher (course_teacher pivot).
        $coursesById = $user->teachingCourses()
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.photo',
            ])
            ->keyBy('id')
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->title,
                    'course_photo' => $course->photo,
                    'subjects' => [],
                ];
            });

        // Subjects assigned to this teacher (subject_teacher pivot).
        $subjects = $user->teachingSubjects()
            ->with('course')
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.credits',
                'subjects.course_id',
                'subjects.photo',
            ]);

        foreach ($subjects as $subject) {
            $course = $subject->course;
            if (! $course) {
                continue;
            }

            if (! $coursesById->has($course->id)) {
                $coursesById->put($course->id, [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->title,
                    'course_photo' => $course->photo,
                    'subjects' => [],
                ]);
            }

            $courseEntry = $coursesById->get($course->id);
            $courseEntry['subjects'][] = [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'credits' => $subject->credits,
                'photo' => $subject->photo,
            ];
            $coursesById->put($course->id, $courseEntry);
        }

        $courses = $coursesById
            ->sortBy('course_code')
            ->values();

        return Inertia::render('Teacher/MyCourses', [
            'courses' => $courses,
        ]);
    }
}
