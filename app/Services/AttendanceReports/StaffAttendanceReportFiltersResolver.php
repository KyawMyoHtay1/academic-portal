<?php

namespace App\Services\AttendanceReports;

use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Services\AttendanceReports\Concerns\BuildsStaffAttendanceReportData;
use Illuminate\Http\Request;

class StaffAttendanceReportFiltersResolver
{
    use BuildsStaffAttendanceReportData;

    /**
     * Resolve and normalize report filters and options.
     *
     * @return array{
     *   0: array<string, mixed>,
     *   1: \Illuminate\Support\Collection<int, string>,
     *   2: \Illuminate\Support\Collection<int, int>,
     *   3: \Illuminate\Support\Collection<int, string>,
     *   4: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *   5: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    public function resolve(Request $request): array
    {
        $defaultThreshold = $this->defaultLowThreshold();
        $defaultCooldown = $this->defaultCooldownDays();
        $threshold = (float) $request->input('threshold', $defaultThreshold);
        if ($threshold < 1 || $threshold > 100) {
            $threshold = $defaultThreshold;
        }

        $cooldownDays = (int) $request->input('cooldown_days', $defaultCooldown);
        if ($cooldownDays < 0 || $cooldownDays > 90) {
            $cooldownDays = $defaultCooldown;
        }

        $dateFrom = trim((string) $request->input('date_from', ''));
        $dateTo = trim((string) $request->input('date_to', ''));
        $sessionDate = trim((string) $request->input('session_date', ''));
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sessionDate)) {
            $sessionDate = '';
        }
        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $trendMode = trim((string) $request->input('trend_mode', 'weekly'));
        if (! in_array($trendMode, ['weekly', 'monthly'], true)) {
            $trendMode = 'weekly';
        }

        $courseId = (int) $request->input('course_id', 0);
        $subjectId = (int) $request->input('subject_id', 0);
        $courseId = $courseId > 0 ? $courseId : null;
        $subjectId = $subjectId > 0 ? $subjectId : null;

        $courseThresholdInput = trim((string) $request->input('course_threshold', ''));
        $subjectThresholdInput = trim((string) $request->input('subject_threshold', ''));
        $courseThreshold = is_numeric($courseThresholdInput) ? (float) $courseThresholdInput : null;
        if ($courseThreshold !== null && ($courseThreshold < 1 || $courseThreshold > 100)) {
            $courseThreshold = null;
        }
        $subjectThreshold = is_numeric($subjectThresholdInput) ? (float) $subjectThresholdInput : null;
        if ($subjectThreshold !== null && ($subjectThreshold < 1 || $subjectThreshold > 100)) {
            $subjectThreshold = null;
        }

        $filters = [
            'programme' => trim((string) $request->input('programme', 'all')),
            'intake_year' => trim((string) $request->input('intake_year', 'all')),
            'semester' => trim((string) $request->input('semester', 'all')),
            'course_id' => $courseId,
            'subject_id' => $subjectId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'session_date' => $sessionDate,
            'threshold' => round($threshold, 2),
            'course_threshold' => $courseThreshold !== null ? round($courseThreshold, 2) : null,
            'subject_threshold' => $subjectThreshold !== null ? round($subjectThreshold, 2) : null,
            'cooldown_days' => $cooldownDays,
            'trend_mode' => $trendMode,
        ];

        $programmes = Student::query()
            ->whereNotNull('programme')
            ->where('programme', '!=', '')
            ->distinct()
            ->orderBy('programme')
            ->pluck('programme')
            ->values();

        $intakeYears = Student::query()
            ->whereNotNull('intake_year')
            ->distinct()
            ->orderByDesc('intake_year')
            ->pluck('intake_year')
            ->values();

        $semesters = Course::query()
            ->whereNotNull('semester')
            ->where('semester', '!=', '')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->values();

        $courses = Course::query()
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'title', 'attendance_threshold'])
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'attendance_threshold' => $course->attendance_threshold !== null
                        ? round((float) $course->attendance_threshold, 2)
                        : null,
                ];
            })
            ->values();

        $subjects = Subject::query()
            ->with('course:id,course_code')
            ->orderBy('subject_code')
            ->get(['id', 'course_id', 'subject_code', 'title', 'attendance_threshold'])
            ->map(function (Subject $subject) {
                return [
                    'id' => $subject->id,
                    'course_id' => $subject->course_id,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'course_code' => $subject->course?->course_code,
                    'attendance_threshold' => $subject->attendance_threshold !== null
                        ? round((float) $subject->attendance_threshold, 2)
                        : null,
                ];
            })
            ->values();

        if ($filters['programme'] !== 'all' && ! $programmes->contains($filters['programme'])) {
            $filters['programme'] = 'all';
        }
        if ($filters['intake_year'] !== 'all' && ! $intakeYears->map(fn ($year) => (string) $year)->contains($filters['intake_year'])) {
            $filters['intake_year'] = 'all';
        }
        if ($filters['semester'] !== 'all' && ! $semesters->contains($filters['semester'])) {
            $filters['semester'] = 'all';
        }

        if ($filters['course_id'] !== null && ! $courses->pluck('id')->contains($filters['course_id'])) {
            $filters['course_id'] = null;
        }

        if ($filters['subject_id'] !== null && ! $subjects->pluck('id')->contains($filters['subject_id'])) {
            $filters['subject_id'] = null;
        }

        if ($filters['subject_id'] !== null && $filters['course_id'] !== null) {
            $selectedSubject = $subjects->firstWhere('id', $filters['subject_id']);
            if (! $selectedSubject || (int) $selectedSubject['course_id'] !== (int) $filters['course_id']) {
                $filters['subject_id'] = null;
            }
        }

        if ($filters['subject_id'] !== null && $filters['course_id'] === null) {
            $selectedSubject = $subjects->firstWhere('id', $filters['subject_id']);
            $filters['course_id'] = $selectedSubject ? (int) $selectedSubject['course_id'] : null;
        }

                return [
            'filters' => $filters,
            'defaults' => [
                'threshold' => $this->defaultLowThreshold(),
                'cooldown_days' => $this->defaultCooldownDays(),
            ],
            'options' => [
                'programmes' => $programmes,
                'intakeYears' => $intakeYears,
                'semesters' => $semesters,
                'courses' => $courses,
                'subjects' => $subjects,
            ],
        ];
    }
}

