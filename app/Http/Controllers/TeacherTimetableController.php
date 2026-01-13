<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherTimetableController extends Controller
{
    /**
     * Display the timetables for subjects assigned to the teacher.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get timetables for subjects assigned to this teacher
        $subjects = $user->teachingSubjects()
            ->with(['timetables' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            }, 'course'])
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.course_id',
                'subjects.photo',
            ]);

        // Group by course for display
        $courses = $subjects->groupBy('course_id')->map(function ($courseSubjects, $courseId) {
            $firstSubject = $courseSubjects->first();
            $course = $firstSubject->course;
            
            $timetables = collect();
            foreach ($courseSubjects as $subject) {
                foreach ($subject->timetables as $entry) {
                    $timetables->push([
                        'id' => $entry->id,
                        'subject_photo' => $subject->photo,
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
        })->values();

        return Inertia::render('Teacher/Timetable/Index', [
            'courses' => $courses,
        ]);
    }
}
