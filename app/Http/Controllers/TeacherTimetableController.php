<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherTimetableController extends Controller
{
    /**
     * Display the timetables for courses assigned to the teacher.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $courses = $user->teachingCourses()
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

        return Inertia::render('Teacher/Timetable/Index', [
            'courses' => $data,
        ]);
    }
}
