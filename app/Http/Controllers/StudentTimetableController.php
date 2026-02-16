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

        if (! $student) {
            return Inertia::render('Student/Timetable/Index', [
                'courses' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        // Get timetables for subjects in enrolled courses
        $courses = $student->courses()
            ->with(['subjects' => function ($query) {
                $query->select('subjects.id', 'subjects.course_id', 'subjects.subject_code', 'subjects.title', 'subjects.photo');
            }, 'subjects.timetables' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.photo',
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
                        'subject_photo' => $subject->photo,
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
                'photo' => $course->photo,
                'timetables' => $timetables,
            ];
        });

        return Inertia::render('Student/Timetable/Index', [
            'courses' => $data,
        ]);
    }
}
