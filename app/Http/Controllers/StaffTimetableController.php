<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Timetables\StoreTimetableRequest;
use App\Http\Requests\Staff\Timetables\UpdateTimetableRequest;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use App\Notifications\TimetableUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffTimetableController extends Controller
{
    private const DAY_ORDER = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    /**
     * Display a listing of timetable entries.
     */
    public function index(Request $request): Response
    {
        $filters = $this->resolveFilters($request);

        $query = Timetable::with(['subject.course', 'creator:id,name,email']);
        $this->applyIndexFilters($query, $filters);

        $timetables = $query
            ->orderByRaw($this->dayOrderSql())
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Timetable $entry) => $this->mapTimetableRow($entry));

        $courses = Course::query()
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'title', 'semester'])
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'semester' => $course->semester,
                ];
            });

        $semesters = Course::query()
            ->whereNotNull('semester')
            ->where('semester', '!=', '')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->values();

        $teachers = User::query()
            ->where('role', 'teacher')
            ->whereHas('teachingSubjects')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Timetables/Index', [
            'timetables' => $timetables,
            'filters' => $filters,
            'courses' => $courses,
            'semesters' => $semesters,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Export timetable report for current filters.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $filters = $this->resolveFilters($request);
        $query = Timetable::with(['subject.course', 'creator:id,name,email']);
        $this->applyIndexFilters($query, $filters);

        $rows = $query
            ->orderByRaw($this->dayOrderSql())
            ->orderBy('start_time')
            ->get()
            ->map(fn (Timetable $entry) => $this->mapTimetableRow($entry));

        $timestamp = now()->format('Ymd_His');

        if ($format === 'csv') {
            return $this->exportCsv($rows, $timestamp);
        }

        $pdf = Pdf::loadView('timetables.report', [
            'title' => 'Timetable Report (Staff)',
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'owner' => auth()->user()?->name,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("timetables_{$timestamp}.pdf");
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

        $conflicts = $this->overlapQuery($subject, $data)->get();
        if ($conflicts->isNotEmpty()) {
            $conflictDetails = $this->mapConflictDetails($conflicts);

            return back()
                ->withErrors([
                    'start_time' => $this->buildConflictMessage($data['day_of_week'], $conflicts),
                    'conflict_details' => json_encode($conflictDetails, JSON_UNESCAPED_UNICODE),
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

        $conflicts = $this->overlapQuery($subject, $data, $timetable->id)->get();
        if ($conflicts->isNotEmpty()) {
            $conflictDetails = $this->mapConflictDetails($conflicts);

            return back()
                ->withErrors([
                    'start_time' => $this->buildConflictMessage($data['day_of_week'], $conflicts),
                    'conflict_details' => json_encode($conflictDetails, JSON_UNESCAPED_UNICODE),
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

    /**
     * @return array<string, string>
     */
    private function resolveFilters(Request $request): array
    {
        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'day' => trim((string) $request->input('day', 'all')),
            'semester' => trim((string) $request->input('semester', 'all')),
            'course_id' => trim((string) $request->input('course_id', 'all')),
            'teacher_id' => trim((string) $request->input('teacher_id', 'all')),
        ];

        if (! in_array($filters['day'], ['all', ...self::DAY_ORDER], true)) {
            $filters['day'] = 'all';
        }
        if ($filters['semester'] === '') {
            $filters['semester'] = 'all';
        }

        return $filters;
    }

    /**
     * @param  Builder<Timetable>  $query
     * @param  array<string, string>  $filters
     */
    private function applyIndexFilters(Builder $query, array $filters): void
    {
        if ($filters['day'] !== '' && $filters['day'] !== 'all') {
            $query->where('day_of_week', $filters['day']);
        }

        if ($filters['semester'] !== '' && $filters['semester'] !== 'all') {
            $query->whereHas('subject.course', function ($courseQuery) use ($filters) {
                $courseQuery->where('semester', $filters['semester']);
            });
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
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Builder<Timetable>
     */
    private function overlapQuery(Subject $subject, array $data, ?int $excludeTimetableId = null): Builder
    {
        $query = Timetable::query()
            ->with(['subject.course'])
            ->whereHas('subject', fn ($q) => $q->where('course_id', $subject->course_id))
            ->where('day_of_week', $data['day_of_week'])
            // strict overlap check: existing_start < new_end AND existing_end > new_start
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($excludeTimetableId !== null) {
            $query->where('id', '!=', $excludeTimetableId);
        }

        return $query;
    }

    /**
     * @param  Collection<int, Timetable>  $conflicts
     */
    private function buildConflictMessage(string $day, Collection $conflicts): string
    {
        $preview = $conflicts
            ->take(3)
            ->map(function (Timetable $entry): string {
                $subjectCode = $entry->subject?->subject_code ?? 'Unknown subject';
                $start = Carbon::parse($entry->start_time)->format('H:i');
                $end = Carbon::parse($entry->end_time)->format('H:i');
                $location = $entry->location ? " @ {$entry->location}" : '';

                return "{$subjectCode} {$start}-{$end}{$location}";
            })
            ->implode('; ');

        $message = "Schedule conflict on {$day}. It clashes with: {$preview}.";
        $remaining = $conflicts->count() - min($conflicts->count(), 3);
        if ($remaining > 0) {
            $message .= " (+{$remaining} more)";
        }

        return $message;
    }

    /**
     * @param  Collection<int, Timetable>  $conflicts
     * @return array<int, array<string, string>>
     */
    private function mapConflictDetails(Collection $conflicts): array
    {
        return $conflicts
            ->map(function (Timetable $entry) {
                $subjectCode = $entry->subject?->subject_code ?? 'Unknown subject';
                $subjectTitle = $entry->subject?->title ?? 'Unknown subject';
                $courseCode = $entry->subject?->course?->course_code ?? 'N/A';
                $courseTitle = $entry->subject?->course?->title ?? 'N/A';
                $start = Carbon::parse($entry->start_time)->format('H:i');
                $end = Carbon::parse($entry->end_time)->format('H:i');

                return [
                    'subject_code' => $subjectCode,
                    'subject_title' => $subjectTitle,
                    'course_code' => $courseCode,
                    'course_title' => $courseTitle,
                    'day_of_week' => (string) $entry->day_of_week,
                    'time_range' => "{$start} - {$end}",
                    'location' => $entry->location ?? '-',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapTimetableRow(Timetable $entry): array
    {
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
            'semester' => $course?->semester,
            'course_photo' => $course?->photo,
            'day_of_week' => $entry->day_of_week,
            'start_time' => $entry->start_time,
            'end_time' => $entry->end_time,
            'location' => $entry->location,
            'created_by' => $entry->created_by,
            'creator_name' => $entry->creator?->name ?? null,
        ];
    }

    private function dayOrderSql(): string
    {
        return "CASE day_of_week
            WHEN 'Monday' THEN 1
            WHEN 'Tuesday' THEN 2
            WHEN 'Wednesday' THEN 3
            WHEN 'Thursday' THEN 4
            WHEN 'Friday' THEN 5
            WHEN 'Saturday' THEN 6
            WHEN 'Sunday' THEN 7
            ELSE 8
        END";
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    private function exportCsv(Collection $rows, string $timestamp): StreamedResponse
    {
        $filename = "timetables_{$timestamp}.csv";

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Subject Code',
                'Subject Title',
                'Course Code',
                'Course Title',
                'Semester',
                'Day',
                'Start Time',
                'End Time',
                'Location',
                'Created By',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['subject_code'],
                    $row['subject_title'],
                    $row['course_code'],
                    $row['course_title'],
                    $row['semester'],
                    $row['day_of_week'],
                    $row['start_time'],
                    $row['end_time'],
                    $row['location'],
                    $row['creator_name'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
