<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TeacherCoursesController extends Controller
{
    /**
     * Display the subjects assigned to the current teacher, grouped by course.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Subjects assigned to this teacher (subject_teacher pivot).
        $subjects = $user->teachingSubjects()
            ->with('course:id,course_code,title,photo,semester')
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.credits',
                'subjects.course_id',
                'subjects.photo',
                'subjects.attendance_threshold',
            ]);

        // Courses directly assigned to this teacher (course_teacher pivot).
        $coursesById = $user->teachingCourses()
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.photo',
                'courses.semester',
            ])
            ->keyBy('id')
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->title,
                    'course_photo' => $course->photo,
                    'semester' => $course->semester,
                    'students_count' => 0,
                    'avg_attendance_rate' => null,
                    'pending_grades_total' => 0,
                    'subjects' => [],
                ];
            });

        foreach ($subjects as $subject) {
            $course = $subject->course;
            if (! $course) {
                continue;
            }

            if (! $coursesById->has($course->id)) {
                $coursesById->put($course->id, [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->title,
                    'course_photo' => $course->photo,
                    'semester' => $course->semester,
                    'students_count' => 0,
                    'avg_attendance_rate' => null,
                    'pending_grades_total' => 0,
                    'subjects' => [],
                ]);
            }
        }

        $courseIds = $coursesById->keys()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $subjectIds = $subjects->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $studentCountsByCourse = [];
        if ($courseIds !== []) {
            $studentCountsByCourse = DB::table('course_student')
                ->select('course_id', DB::raw('COUNT(DISTINCT student_id) AS students_count'))
                ->whereIn('course_id', $courseIds)
                ->whereIn('status', ['approved', 'withdrawal_pending'])
                ->groupBy('course_id')
                ->pluck('students_count', 'course_id')
                ->map(fn ($count) => (int) $count)
                ->all();
        }

        $attendanceBySubject = [];
        if ($subjectIds !== []) {
            $attendanceBySubject = DB::table('attendances')
                ->select(
                    'subject_id',
                    DB::raw('COUNT(*) AS total_records'),
                    DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_records"),
                    DB::raw('MAX(date) AS last_marked_on')
                )
                ->whereIn('subject_id', $subjectIds)
                ->groupBy('subject_id')
                ->get()
                ->keyBy('subject_id')
                ->map(function ($row) {
                    return [
                        'total_records' => (int) ($row->total_records ?? 0),
                        'present_records' => (int) ($row->present_records ?? 0),
                        'last_marked_on' => $row->last_marked_on,
                    ];
                })
                ->all();
        }

        $gradeStatsBySubject = [];
        if ($subjectIds !== []) {
            $gradeStatsBySubject = DB::table('grades')
                ->select(
                    'subject_id',
                    DB::raw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft_count"),
                    DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count"),
                    DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count")
                )
                ->whereIn('subject_id', $subjectIds)
                ->groupBy('subject_id')
                ->get()
                ->keyBy('subject_id')
                ->map(function ($row) {
                    return [
                        'draft_count' => (int) ($row->draft_count ?? 0),
                        'pending_count' => (int) ($row->pending_count ?? 0),
                        'approved_count' => (int) ($row->approved_count ?? 0),
                    ];
                })
                ->all();
        }

        foreach ($subjects as $subject) {
            $course = $subject->course;
            if (! $course) {
                continue;
            }

            $attendance = $attendanceBySubject[$subject->id] ?? [
                'total_records' => 0,
                'present_records' => 0,
                'last_marked_on' => null,
            ];
            $totalRecords = (int) $attendance['total_records'];
            $presentRecords = (int) $attendance['present_records'];
            $absentRecords = max($totalRecords - $presentRecords, 0);
            $attendanceRate = $totalRecords > 0
                ? round(($presentRecords / $totalRecords) * 100, 1)
                : null;

            $gradeStats = $gradeStatsBySubject[$subject->id] ?? [
                'draft_count' => 0,
                'pending_count' => 0,
                'approved_count' => 0,
            ];

            $courseEntry = $coursesById->get($course->id);
            $courseEntry['subjects'][] = [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'credits' => $subject->credits,
                'photo' => $subject->photo,
                'student_count' => (int) ($studentCountsByCourse[$course->id] ?? 0),
                'attendance_rate' => $attendanceRate,
                'attendance_records_total' => $totalRecords,
                'attendance_records_present' => $presentRecords,
                'attendance_records_absent' => $absentRecords,
                'last_attendance_date' => $attendance['last_marked_on'],
                'draft_grades' => (int) $gradeStats['draft_count'],
                'pending_grades' => (int) $gradeStats['pending_count'],
                'approved_grades' => (int) $gradeStats['approved_count'],
                'attendance_threshold' => $subject->attendance_threshold !== null
                    ? round((float) $subject->attendance_threshold, 1)
                    : null,
            ];
            $coursesById->put($course->id, $courseEntry);
        }

        $courses = $coursesById
            ->map(function (array $course) use ($studentCountsByCourse) {
                $subjectRates = collect($course['subjects'])
                    ->pluck('attendance_rate')
                    ->filter(fn ($rate) => $rate !== null)
                    ->values();

                $course['students_count'] = (int) ($studentCountsByCourse[$course['id']] ?? 0);
                $course['avg_attendance_rate'] = $subjectRates->count() > 0
                    ? round((float) $subjectRates->avg(), 1)
                    : null;
                $course['pending_grades_total'] = (int) collect($course['subjects'])->sum('pending_grades');

                return $course;
            })
            ->sortBy('course_code')
            ->values();

        return Inertia::render('Teacher/MyCourses', [
            'courses' => $courses,
        ]);
    }
}
