<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentTimetableController extends Controller
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

        $data = $this->buildCourseData($student);

        return Inertia::render('Student/Timetable/Index', [
            'courses' => $data,
        ]);
    }

    /**
     * Export student timetable.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
            return redirect()
                ->route('student.timetable.index')
                ->with('error', 'No student record found for timetable export.');
        }

        $courses = $this->buildCourseData($student);
        $courseId = (string) $request->input('course_id', 'all');
        $semester = trim((string) $request->input('semester', 'all'));
        if ($semester === '') {
            $semester = 'all';
        }

        $rows = collect($courses)
            ->filter(function (array $course) use ($courseId): bool {
                if ($courseId === '' || $courseId === 'all') {
                    return true;
                }

                return (string) $course['id'] === $courseId;
            })
            ->filter(function (array $course) use ($semester): bool {
                if ($semester === 'all') {
                    return true;
                }

                return (string) ($course['semester'] ?? '') === $semester;
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
                        'semester' => $course['semester'] ?? null,
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
            'title' => 'Timetable Report (Student)',
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'owner' => $user?->name,
            'filters' => [
                'course_id' => $courseId,
                'semester' => $semester,
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download("student_timetable_{$timestamp}.pdf");
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildCourseData($student): Collection
    {
        $dayOrder = array_flip(self::DAY_ORDER);

        // Get timetables for subjects in enrolled courses.
        $courses = $student->courses()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
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
                'courses.semester',
                'courses.photo',
            ]);

        return $courses->map(function ($course) use ($dayOrder) {
            $timetables = collect();

            // Collect all timetables from all subjects in this course.
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

            // Sort by day and time (calendar order).
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
                'semester' => $course->semester,
                'photo' => $course->photo,
                'timetables' => $timetables,
            ];
        });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    private function exportCsv(Collection $rows, string $timestamp): StreamedResponse
    {
        $filename = "student_timetable_{$timestamp}.csv";

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
                'Semester',
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
                    $row['semester'],
                    $row['location'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
