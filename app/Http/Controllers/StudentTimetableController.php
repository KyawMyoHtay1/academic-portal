<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentTimetableController extends Controller
{
    /**
     * Display the timetables for the student's enrolled courses.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return Inertia::render('Student/Timetable/Index', [
                'courses' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        $courses = $student->courses()
            ->with(['timetables' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
            ]);

        $data = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'timetables' => $course->timetables->map(function ($entry) {
                    return [
                        'id' => $entry->id,
                        'day_of_week' => $entry->day_of_week,
                        'start_time' => $entry->start_time,
                        'end_time' => $entry->end_time,
                        'location' => $entry->location,
                    ];
                }),
            ];
        });

        return Inertia::render('Student/Timetable/Index', [
            'courses' => $data,
        ]);
    }
}
