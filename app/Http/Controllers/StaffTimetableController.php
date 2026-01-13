<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Timetable;
use App\Notifications\TimetableUpdated;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffTimetableController extends Controller
{
    /**
     * Display a listing of timetable entries.
     */
    public function index(): Response
    {
        $timetables = Timetable::with('course')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(15)
            ->through(function (Timetable $entry) {
                return [
                    'id' => $entry->id,
                    'course_code' => $entry->course->course_code,
                    'course_title' => $entry->course->title,
                    'day_of_week' => $entry->day_of_week,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'location' => $entry->location,
                ];
            });

        return Inertia::render('Admin/Timetables/Index', [
            'timetables' => $timetables,
        ]);
    }

    /**
     * Show the form for creating a new timetable entry.
     */
    public function create(): Response
    {
        $courses = Course::orderBy('course_code')
            ->get(['id', 'course_code', 'title']);

        return Inertia::render('Admin/Timetables/Create', [
            'courses' => $courses,
        ]);
    }

    /**
     * Store a newly created timetable entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'day_of_week' => ['required', 'string', 'max:20'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $timetable = Timetable::create($data);

        // Notify enrolled students and assigned teachers
        $course = $timetable->course;
        $notifiables = collect()
            ->merge($course->students->pluck('user'))
            ->merge($course->teachers);

        $notifiables->filter()->each(function ($user) use ($timetable) {
            $user->notify(new TimetableUpdated($timetable, 'created'));
        });

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Timetable entry created successfully.');
    }

    /**
     * Show the form for editing the specified timetable entry.
     */
    public function edit(Timetable $timetable): Response
    {
        $courses = Course::orderBy('course_code')
            ->get(['id', 'course_code', 'title']);

        // Format times to H:i format (remove seconds) for HTML time input
        $startTime = $timetable->start_time ? Carbon::parse($timetable->start_time)->format('H:i') : null;
        $endTime = $timetable->end_time ? Carbon::parse($timetable->end_time)->format('H:i') : null;

        return Inertia::render('Admin/Timetables/Edit', [
            'timetable' => [
                'id' => $timetable->id,
                'course_id' => $timetable->course_id,
                'day_of_week' => $timetable->day_of_week,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $timetable->location,
            ],
            'courses' => $courses,
        ]);
    }

    /**
     * Update the specified timetable entry.
     */
    public function update(Request $request, Timetable $timetable): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'day_of_week' => ['required', 'string', 'max:20'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $timetable->update($data);

        // Notify enrolled students and assigned teachers
        $course = $timetable->course;
        $notifiables = collect()
            ->merge($course->students->pluck('user'))
            ->merge($course->teachers);

        $notifiables->filter()->each(function ($user) use ($timetable) {
            $user->notify(new TimetableUpdated($timetable, 'updated'));
        });

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Timetable entry updated successfully.');
    }

    /**
     * Remove the specified timetable entry.
     */
    public function destroy(Timetable $timetable): RedirectResponse
    {
        $timetable->delete();

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Timetable entry deleted successfully.');
    }
}
