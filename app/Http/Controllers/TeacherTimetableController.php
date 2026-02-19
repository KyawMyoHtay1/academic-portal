<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherTimetableController extends Controller
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
     * Display the timetables for subjects assigned to the teacher.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $courses = $this->buildCourseData($user);

        return Inertia::render('Teacher/Timetable/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Export teacher timetable.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $user = Auth::user();
        $courses = $this->buildCourseData($user);
        $courseId = (string) $request->input('course_id', 'all');

        $rows = collect($courses)
            ->filter(function (array $course) use ($courseId): bool {
                if ($courseId === '' || $courseId === 'all') {
                    return true;
                }

                return (string) $course['id'] === $courseId;
            })
            ->flatMap(function (array $course): Collection {
                return collect($course['timetables'])->map(function (array $entry) use ($course): array {
                    return [
                        'day_of_week' => $entry['day_of_week'],
                        'start_time' => $entry['start_time'],
                        'end_time' => $entry['end_time'],
                        'subject_code' => $entry['subject_code'],
                        'subject_title' => $entry['subject_title'],
                        'course_code' => $course['course_code'],
                        'course_title' => $course['title'],
                        'location' => $entry['location'],
                    ];
                });
            })
            ->values();

        $timestamp = now()->format('Ymd_His');

        if ($format === 'csv') {
            return $this->exportCsv($rows, $timestamp);
        }

        $pdf = Pdf::loadView('timetables.report', [
            'title' => 'Timetable Report (Teacher)',
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'owner' => $user?->name,
            'filters' => [
                'course_id' => $courseId,
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download("teacher_timetable_{$timestamp}.pdf");
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildCourseData($user): Collection
    {
        $dayOrder = array_flip(self::DAY_ORDER);

        // Get timetables for subjects assigned to this teacher.
        $subjects = $user->teachingSubjects()
            ->with(['timetables' => function ($query) {
                $query->orderBy('start_time');
            }, 'course'])
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.course_id',
                'subjects.photo',
            ]);

        // Group by course for display.
        return $subjects->groupBy('course_id')->map(function ($courseSubjects) use ($dayOrder) {
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

            // Sort by day and time (calendar order, not alphabetic order).
            $timetables = $timetables
                ->sortBy(function (array $entry) use ($dayOrder) {
                    $dayRank = $dayOrder[$entry['day_of_week']] ?? 99;

                    return sprintf('%02d-%s', $dayRank, (string) $entry['start_time']);
                })
                ->values();

            return [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'photo' => $course->photo,
                'timetables' => $timetables,
            ];
        })->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    private function exportCsv(Collection $rows, string $timestamp): StreamedResponse
    {
        $filename = "teacher_timetable_{$timestamp}.csv";

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Day',
                'Start Time',
                'End Time',
                'Subject Code',
                'Subject Title',
                'Course Code',
                'Course Title',
                'Location',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['day_of_week'],
                    $row['start_time'],
                    $row['end_time'],
                    $row['subject_code'],
                    $row['subject_title'],
                    $row['course_code'],
                    $row['course_title'],
                    $row['location'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
