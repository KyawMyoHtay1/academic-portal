<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Timetables\StoreTimetableRequest;
use App\Http\Requests\Staff\Timetables\UpdateTimetableRequest;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use App\Notifications\TimetableUpdated;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffTimetableController extends Controller
{
    /**
     * Display a listing of timetable entries.
     */
    public function index(): Response
    {
        $filters = [
            'q' => trim((string) request('q', '')),
            'day' => trim((string) request('day', 'all')),
            'course_id' => trim((string) request('course_id', 'all')),
            'teacher_id' => trim((string) request('teacher_id', 'all')),
        ];
        if (! in_array($filters['day'], ['all', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], true)) {
            $filters['day'] = 'all';
        }

        $query = Timetable::with(['subject.course', 'creator:id,name,email']);

        if ($filters['day'] !== '' && $filters['day'] !== 'all') {
            $query->where('day_of_week', $filters['day']);
        }

        if ($filters['course_id'] !== '' && $filters['course_id'] !== 'all') {
            $query->whereHas('subject', function ($subjectQuery) use ($filters) {
                $subjectQuery->where('course_id', (int) $filters['course_id']);
            });
        }

        if ($filters['teacher_id'] !== '' && $filters['teacher_id'] !== 'all') {
            $query->whereHas('subject.teachers', function ($teacherQuery) use ($filters) {
                $teacherQuery->where('users.id', (int) $filters['teacher_id']);
            });
        }

        if ($filters['q'] !== '') {
            $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['q']).'%';

            $query->where(function ($inner) use ($like) {
                $inner->where('location', 'like', $like)
                    ->orWhereHas('subject', function ($subjectQuery) use ($like) {
                        $subjectQuery
                            ->where('subject_code', 'like', $like)
                            ->orWhere('title', 'like', $like)
                            ->orWhereHas('course', function ($courseQuery) use ($like) {
                                $courseQuery
                                    ->where('course_code', 'like', $like)
                                    ->orWhere('title', 'like', $like);
                            })
                            ->orWhereHas('teachers', function ($teacherQuery) use ($like) {
                                $teacherQuery->where('name', 'like', $like);
                            });
                    })
                    ->orWhereHas('creator', function ($creatorQuery) use ($like) {
                        $creatorQuery->where('name', 'like', $like);
                    });
            });
        }

        $timetables = $query
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString()
            ->through(function (Timetable $entry) {
                $subject = $entry->subject;
                $course = $subject?->course;

                return [
                    'id' => $entry->id,
                    'subject_id' => $entry->subject_id,
                    'subject_code' => $subject?->subject_code,
                    'subject_title' => $subject?->title,
                    'subject_photo' => $subject?->photo,
                    'course_code' => $course?->course_code,
                    'course_title' => $course?->title,
                    'course_photo' => $course?->photo,
                    'day_of_week' => $entry->day_of_week,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'location' => $entry->location,
                    'created_by' => $entry->created_by,
                    'creator_name' => $entry->creator?->name ?? null,
                ];
            });

        $courses = Course::query()
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'title'])
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                ];
            });

        $teachers = User::query()
            ->where('role', 'teacher')
            ->whereHas('teachingSubjects')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Timetables/Index', [
            'timetables' => $timetables,
            'filters' => $filters,
            'courses' => $courses,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Show the form for creating a new timetable entry.
     */
    public function create(): Response
    {
        $subjects = Subject::with('course')
            ->orderBy('subject_code')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                ];
            });

        return Inertia::render('Admin/Timetables/Create', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a newly created timetable entry.
     */
    public function store(StoreTimetableRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subject = Subject::findOrFail($data['subject_id']);

        // Basic conflict detection (same course + same day + overlapping time) via subject
        $overlap = Timetable::query()
            ->whereHas('subject', fn ($q) => $q->where('course_id', $subject->course_id))
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors([
                    'start_time' => 'This entry overlaps with an existing session for the same course/day.',
                ])
                ->withInput();
        }

        $timetable = Timetable::create([
            'subject_id' => $data['subject_id'],
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'location' => $data['location'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        // Notify enrolled students and assigned teachers
        $course = $subject->course;
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
        $subjects = Subject::with('course')
            ->orderBy('subject_code')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                ];
            });

        // Format times to H:i format (remove seconds) for HTML time input
        $startTime = $timetable->start_time ? Carbon::parse($timetable->start_time)->format('H:i') : null;
        $endTime = $timetable->end_time ? Carbon::parse($timetable->end_time)->format('H:i') : null;

        return Inertia::render('Admin/Timetables/Edit', [
            'timetable' => [
                'id' => $timetable->id,
                'subject_id' => $timetable->subject_id,
                'day_of_week' => $timetable->day_of_week,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $timetable->location,
            ],
            'subjects' => $subjects,
        ]);
    }

    /**
     * Update the specified timetable entry.
     */
    public function update(UpdateTimetableRequest $request, Timetable $timetable): RedirectResponse
    {
        $data = $request->validated();

        $subject = Subject::findOrFail($data['subject_id']);

        $overlap = Timetable::query()
            ->where('id', '!=', $timetable->id)
            ->whereHas('subject', fn ($q) => $q->where('course_id', $subject->course_id))
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors([
                    'start_time' => 'This entry overlaps with an existing session for the same course/day.',
                ])
                ->withInput();
        }

        $timetable->update([
            'subject_id' => $data['subject_id'],
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'location' => $data['location'] ?? null,
        ]);

        // Notify enrolled students and assigned teachers
        $course = $subject->course;
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
