<?php

namespace App\Services\AttendanceReports;

use App\Models\Attendance;
use App\Services\AttendanceReports\Concerns\BuildsStaffAttendanceReportData;
use Illuminate\Http\Request;

class StaffAttendanceReportExportBuilder
{
    use BuildsStaffAttendanceReportData;

    public function __construct(
        private readonly StaffAttendanceReportFiltersResolver $filtersResolver,
    ) {}

    /**
     * @return array{filters: array<string, mixed>, overall: array<string, int|float>, rows: \Illuminate\Support\Collection<int, array<string, mixed>>}
     */
    public function build(Request $request): array
    {
        $resolved = $this->filtersResolver->resolve($request);
        $filters = $resolved['filters'];

        $records = Attendance::query()
            ->with([
                'student:id,user_id,student_no,programme,intake_year',
                'student.user:id,name,email',
                'subject.course:id,course_code,semester',
            ])
            ->tap(function ($query) use ($filters) {
                $this->applyAttendanceFilters($query, $filters);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function ($attendance) {
                $student = $attendance->student;
                $subject = $attendance->subject;
                $course = $subject?->course;

                return [
                    'date' => $attendance->date?->format('Y-m-d'),
                    'student_no' => $student?->student_no ?? 'N/A',
                    'student_name' => $student?->user?->name ?? $student?->full_name ?? 'N/A',
                    'programme' => $student?->programme ?? 'N/A',
                    'intake_year' => $student?->intake_year ?? 'N/A',
                    'subject_code' => $subject?->subject_code ?? 'N/A',
                    'subject_title' => $subject?->title ?? 'N/A',
                    'course_code' => $course?->course_code ?? 'N/A',
                    'semester' => $course?->semester ?? 'N/A',
                    'status' => $attendance->status,
                ];
            });

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
        return [
            'filters' => $filters,
            'overall' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'rate' => $rate,
            ],
            'rows' => $records,
        ];
    }
}
