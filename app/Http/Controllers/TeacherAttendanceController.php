<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\Attendance\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\AttendanceAlert;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a list of subjects the teacher can mark attendance for.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get subjects assigned to this teacher
        $subjects = $user->teachingSubjects()
            ->with('course')
            ->orderBy('subject_code')
            ->get([
                'subjects.id',
                'subjects.subject_code',
                'subjects.title',
                'subjects.course_id',
                'subjects.photo',
            ])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'photo' => $subject->photo,
                    'photo_thumb' => ImageService::tablePath($subject->photo),
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                ];
            });

        return Inertia::render('Teacher/Attendance/Index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the form for marking attendance for a specific subject.
     */
    public function show(Request $request, Subject $subject): Response
    {
        $user = Auth::user();

        // Verify teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        // Get enrolled students for the subject's course
        $students = $subject->course->students()
            ->with('user')
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->get([
                'students.id',
                'students.user_id',
                'students.student_no',
                'students.photo',
            ])
            ->sortBy(fn ($s) => strtolower((string) ($s->user?->name ?? '')))
            ->values()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'student_no' => $student->student_no,
                    'photo' => $student->photo,
                    'photo_thumb' => ImageService::tablePath($student->photo),
                    'full_name' => $student->user?->name ?? $student->full_name,
                ];
            });

        // Attendance overview per student (for this subject)
        $studentIds = $students->pluck('id');

        $rawSummary = Attendance::query()
            ->selectRaw('student_id, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $studentIds)
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $summary = [];
        foreach ($rawSummary as $studentId => $row) {
            $total = (int) ($row->total ?? 0);
            $present = (int) ($row->present ?? 0);
            $percentage = $total > 0 ? round(($present / $total) * 100) : null;

            $summary[$studentId] = [
                'total' => $total,
                'present' => $present,
                'percentage' => $percentage,
            ];
        }

        // Total distinct sessions (dates) held for this subject
        $totalSessions = Attendance::query()
            ->where('subject_id', $subject->id)
            ->distinct('date')
            ->count('date');

        $historyFilters = [
            'date_from' => trim((string) $request->input('date_from', '')),
            'date_to' => trim((string) $request->input('date_to', '')),
        ];
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $historyFilters['date_from'])) {
            $historyFilters['date_from'] = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $historyFilters['date_to'])) {
            $historyFilters['date_to'] = '';
        }
        if (
            $historyFilters['date_from'] !== '' &&
            $historyFilters['date_to'] !== '' &&
            $historyFilters['date_from'] > $historyFilters['date_to']
        ) {
            [$historyFilters['date_from'], $historyFilters['date_to']] = [
                $historyFilters['date_to'],
                $historyFilters['date_from'],
            ];
        }

        $historyBaseQuery = Attendance::query()
            ->where('subject_id', $subject->id)
            ->when($historyFilters['date_from'] !== '', function ($query) use ($historyFilters) {
                $query->whereDate('date', '>=', $historyFilters['date_from']);
            })
            ->when($historyFilters['date_to'] !== '', function ($query) use ($historyFilters) {
                $query->whereDate('date', '<=', $historyFilters['date_to']);
            });

        $sessionHistory = (clone $historyBaseQuery)
            ->selectRaw('date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(40)
            ->get()
            ->map(function ($row) {
                $total = (int) ($row->total ?? 0);
                $present = (int) ($row->present ?? 0);
                $absent = max($total - $present, 0);
                return [
                    'date' => $row->date?->format('Y-m-d') ?? (string) $row->date,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $detailDates = $sessionHistory
            ->take(12)
            ->pluck('date')
            ->filter()
            ->values()
            ->all();

        $sessionDetails = [];
        if (count($detailDates) > 0) {
            $detailRows = Attendance::query()
                ->with([
                    'student:id,user_id,student_no',
                    'student.user:id,name',
                ])
                ->where('subject_id', $subject->id)
                ->whereIn('date', $detailDates)
                ->orderByDesc('date')
                ->orderBy('student_id')
                ->get(['id', 'student_id', 'date', 'status']);

            $sessionDetails = $detailRows
                ->groupBy(function ($attendance) {
                    return $attendance->date?->format('Y-m-d') ?? (string) $attendance->date;
                })
                ->map(function ($rows) {
                    return $rows->map(function ($attendance) {
                        return [
                            'id' => $attendance->id,
                            'student_id' => $attendance->student_id,
                            'student_no' => $attendance->student?->student_no,
                            'student_name' => $attendance->student?->user?->name
                                ?? $attendance->student?->full_name,
                            'status' => $attendance->status,
                        ];
                    })->values()->all();
                })
                ->toArray();
        }

        return Inertia::render('Teacher/Attendance/Mark', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'students' => $students,
            'summary' => $summary,
            'totalSessions' => $totalSessions,
            'sessionHistory' => $sessionHistory,
            'sessionDetails' => $sessionDetails,
            'historyFilters' => $historyFilters,
        ]);
    }

    /**
     * Store attendance records for a subject.
     */
    public function store(StoreAttendanceRequest $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Verify teacher is assigned to this subject
        if (! $user->teachingSubjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'You are not assigned to this subject.');
        }

        $data = $request->validated();

        // Verify all students are enrolled in the subject's course
        $enrolledStudentIds = $subject->course->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->pluck('students.id')
            ->toArray();
        foreach ($data['attendance'] as $record) {
            if (! in_array($record['student_id'], $enrolledStudentIds)) {
                return redirect()
                    ->back()
                    ->withErrors(['attendance' => 'One or more students are not enrolled in this course.']);
            }
        }

        // Save or update attendance records and notify students
        foreach ($data['attendance'] as $record) {
            $attendance = Attendance::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'student_id' => $record['student_id'],
                    'date' => $data['date'],
                ],
                [
                    'status' => $record['status'],
                ]
            );

            $student = Student::find($record['student_id']);
            if ($student && $student->user) {
                $student->user->notify(new AttendanceAlert($attendance));
            }
        }

        return redirect()
            ->route('teacher.attendance.show', $subject->id)
            ->with('success', 'Attendance marked successfully.');
    }
}
