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

        // Get timetables for subjects in assigned courses
        $courses = $user->teachingCourses()
            ->with(['subjects.timetables' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
            ]);

        $data = $courses->map(function ($course) {
            $timetables = collect();
            
            // Collect all timetables from all subjects in this course
            foreach ($course->subjects as $subject) {
                foreach ($subject->timetables as $entry) {
                    $timetables->push([
                        'id' => $entry->id,
                        'subject_code' => $subject->subject_code,
                        'subject_title' => $subject->title,
                        'day_of_week' => $entry->day_of_week,
                        'start_time' => $entry->start_time,
                        'end_time' => $entry->end_time,
                        'location' => $entry->location,
                    ]);
                }
            }
            
            // Sort by day and time
            $timetables = $timetables->sortBy([
                ['day_of_week', 'asc'],
                ['start_time', 'asc'],
            ])->values();
            
            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'timetables' => $timetables,
            ];
        });

        return Inertia::render('Teacher/Timetable/Index', [
            'courses' => $data,
        ]);
    }
}
