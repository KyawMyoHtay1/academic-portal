<?php

namespace App\Http\Controllers;

use App\Services\AttendanceReports\StaffAttendanceReportExportBuilder;
use App\Services\AttendanceReports\StaffAttendanceReportPageBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StaffAttendanceReportController extends Controller
{
    public function __construct(
        private readonly StaffAttendanceReportPageBuilder $pageBuilder,
        private readonly StaffAttendanceReportExportBuilder $exportBuilder,
    ) {}

    /**
     * Display attendance summary report.
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Admin/Attendance/Report', $this->pageBuilder->build($request));
    }

    /**
     * Export filtered attendance report.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $export = $this->exportBuilder->build($request);
        $filters = $export['filters'];
        $records = $export['rows'];
        $overall = $export['overall'];

        $timestamp = now()->format('Ymd_His');
        if ($format === 'csv') {
            $filename = "attendance_report_{$timestamp}.csv";

            return response()->streamDownload(function () use ($records): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Date',
                    'Student No',
                    'Student Name',
                    'Programme',
                    'Intake Year',
                    'Subject Code',
                    'Subject Title',
                    'Course Code',
                    'Semester',
                    'Status',
                ]);

                foreach ($records as $row) {
                    fputcsv($handle, [
                        $row['date'],
                        $row['student_no'],
                        $row['student_name'],
                        $row['programme'],
                        $row['intake_year'],
                        $row['subject_code'],
                        $row['subject_title'],
                        $row['course_code'],
                        $row['semester'],
                        $row['status'],
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $pdf = Pdf::loadView('attendance.staff_report', [
            'filters' => $filters,
            'overall' => $overall,
            'rows' => $records,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("attendance_report_{$timestamp}.pdf");
    }
}
