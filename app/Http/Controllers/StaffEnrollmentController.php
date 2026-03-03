<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffEnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    /**
     * Display and filter course enrollments for staff.
     */
    public function index(Request $request): Response
    {
        $filters = $this->resolveFilters($request);
        $allowedSorts = $this->allowedSorts();
        $sortColumn = $allowedSorts[$filters['sort_by']] ?? $allowedSorts['requested_at'];

        $query = $this->baseEnrollmentQuery($filters);

        // Order by requested sort + stable fallback.
        $query->orderBy($sortColumn, $filters['sort_dir'])
            ->orderByDesc('course_student.updated_at')
            ->orderByDesc('course_student.created_at');

        $perPage = 15;
        $enrollments = $query
            ->paginate($perPage)
            ->withQueryString();

        $logsByEnrollment = $this->fetchEnrollmentLogsForPage($enrollments->items());
        $enrollments = $enrollments->through(function ($row) use ($logsByEnrollment) {
            $enrollmentId = (int) ($row->enrollment_id ?? 0);

            return $this->mapEnrollmentRow(
                $row,
                $logsByEnrollment[$enrollmentId] ?? []
            );
        });

        // Status counts for tabs
        $statusCountsRaw = DB::table('course_student')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'pending' => (int) ($statusCountsRaw['pending'] ?? 0),
            'approved' => (int) ($statusCountsRaw['approved'] ?? 0),
            'rejected' => (int) ($statusCountsRaw['rejected'] ?? 0),
            'withdrawal_pending' => (int) ($statusCountsRaw['withdrawal_pending'] ?? 0),
        ];

        $statusCounts['all'] = array_sum($statusCounts);

        $today = now()->toDateString();
        $startOfWeek = now()->copy()->startOfWeek();
        $now = now();
        $totalEnrollments = DB::table('course_student')->count();
        $approvalsToday = DB::table('course_student')
            ->where('status', 'approved')
            ->whereDate('updated_at', $today)
            ->count();
        $approvalsWeek = DB::table('course_student')
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startOfWeek, $now])
            ->count();
        $rejectionsTotal = DB::table('course_student')
            ->where('status', 'rejected')
            ->count();
        $rejectionRate = $totalEnrollments > 0
            ? round(($rejectionsTotal / $totalEnrollments) * 100, 1)
            : 0;

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'filters' => [
                'status' => $filters['status'],
                'search' => $filters['search'],
                'sort_by' => $filters['sort_by'],
                'sort_dir' => $filters['sort_dir'],
                'date_from' => $filters['date_from'],
                'date_to' => $filters['date_to'],
            ],
            'counts' => $statusCounts,
            'overview' => [
                'total' => $totalEnrollments,
                'approvals_today' => $approvalsToday,
                'approvals_week' => $approvalsWeek,
                'rejection_rate' => $rejectionRate,
            ],
        ]);
    }

    /**
     * Export enrollments using the current filters.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $filters = $this->resolveFilters($request);
        $allowedSorts = $this->allowedSorts();
        $sortColumn = $allowedSorts[$filters['sort_by']] ?? $allowedSorts['requested_at'];

        $rows = $this->baseEnrollmentQuery($filters)
            ->orderBy($sortColumn, $filters['sort_dir'])
            ->orderByDesc('course_student.updated_at')
            ->orderByDesc('course_student.created_at')
            ->get()
            ->map(fn ($row) => $this->mapEnrollmentRow($row, []));

        $timestamp = now()->format('Ymd_His');

        if ($format === 'csv') {
            $filename = "enrollments_{$timestamp}.csv";

            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Enrollment ID',
                    'Student No',
                    'Student Name',
                    'Student Email',
                    'Programme',
                    'Course Code',
                    'Course Title',
                    'Credits',
                    'Semester',
                    'Status',
                    'Requested At',
                ]);

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row['enrollment_id'],
                        $row['student_no'],
                        $row['student_name'],
                        $row['student_email'],
                        $row['programme'],
                        $row['course_code'],
                        $row['course_title'],
                        $row['credits'],
                        $row['semester'],
                        $row['status'],
                        $row['requested_at'],
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $pdf = Pdf::loadView('enrollments.report', [
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("enrollments_{$timestamp}.pdf");
    }

    /**
     * Approve a pending enrollment.
     */
    public function approve($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->approveEnrollment($enrollment);

        return back()
            ->with($result['level'], $result['message']);
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject(Request $request, $enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->rejectEnrollment(
            $enrollment,
            $this->normalizeDecisionReason($request->input('reason'))
        );

        return back()
            ->with($result['level'], $result['message']);
    }

    /**
     * Approve a withdrawal request (removes the enrollment).
     */
    public function approveWithdrawal($enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->approveWithdrawal($enrollment);

        return back()
            ->with($result['level'], $result['message']);
    }

    /**
     * Reject a withdrawal request (keeps the enrollment as approved).
     */
    public function rejectWithdrawal(Request $request, $enrollment): RedirectResponse
    {
        $result = $this->enrollmentService->rejectWithdrawal(
            $enrollment,
            $this->normalizeDecisionReason($request->input('reason'))
        );

        return back()
            ->with($result['level'], $result['message']);
    }

    /**
     * @return array<string, string>
     */
    private function resolveFilters(Request $request): array
    {
        $filters = $request->only([
            'status',
            'search',
            'sort_by',
            'sort_dir',
            'date_from',
            'date_to',
        ]);

        $allowedSorts = $this->allowedSorts();
        $status = (string) ($filters['status'] ?? 'pending');
        $search = trim((string) ($filters['search'] ?? ''));
        $requestedSortBy = (string) ($filters['sort_by'] ?? 'requested_at');
        $sortBy = array_key_exists($requestedSortBy, $allowedSorts)
            ? $requestedSortBy
            : 'requested_at';
        $sortDir = ($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $dateFrom = trim((string) ($filters['date_from'] ?? ''));
        $dateTo = trim((string) ($filters['date_to'] ?? ''));
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }
        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        return [
            'status' => $status,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
    }

    private function normalizeDecisionReason(mixed $rawReason): ?string
    {
        if (! is_scalar($rawReason)) {
            return null;
        }

        $reason = trim((string) $rawReason);
        if ($reason === '') {
            return null;
        }

        return mb_substr($reason, 0, 1000);
    }

    /**
     * @return array<string, string>
     */
    private function allowedSorts(): array
    {
        return [
            'requested_at' => 'course_student.updated_at',
            'student_no' => 'students.student_no',
            'student_name' => 'students.full_name',
            'programme' => 'students.programme',
            'status' => 'course_student.status',
            'course_code' => 'courses.course_code',
            'semester' => 'courses.semester',
        ];
    }

    /**
     * @param  array<string, string>  $filters
     */
    private function baseEnrollmentQuery(array $filters)
    {
        $query = DB::table('course_student')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->select(
                'course_student.id as enrollment_id',
                'course_student.created_at',
                'course_student.updated_at',
                'course_student.status',
                'courses.id as course_id',
                'courses.course_code',
                'courses.title as course_title',
                'courses.credits',
                'courses.semester',
                'courses.photo as course_photo',
                'students.id as student_id',
                'students.student_no',
                'students.full_name as student_name',
                'students.email as student_email',
                'students.programme',
                'students.photo as student_photo'
            );

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $like = '%'.$search.'%';
                $q->where('students.full_name', 'like', $like)
                    ->orWhere('students.student_no', 'like', $like)
                    ->orWhere('students.email', 'like', $like)
                    ->orWhere('students.programme', 'like', $like)
                    ->orWhere('courses.course_code', 'like', $like)
                    ->orWhere('courses.title', 'like', $like);
            });
        }

        if ($filters['status'] !== 'all') {
            $query->where('course_student.status', $filters['status']);
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('course_student.updated_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to'] !== '') {
            $query->whereDate('course_student.updated_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * @param  object  $row
     * @param  array<int, array<string, mixed>>  $auditTrail
     * @return array<string, mixed>
     */
    private function mapEnrollmentRow(object $row, array $auditTrail): array
    {
        return [
            'enrollment_id' => $row->enrollment_id,
            'status' => $row->status,
            'requested_at' => $row->updated_at ?? $row->created_at,
            'course_id' => $row->course_id,
            'course_code' => $row->course_code,
            'course_title' => $row->course_title,
            'credits' => $row->credits,
            'semester' => $row->semester,
            'course_photo' => $row->course_photo,
            'student_id' => $row->student_id,
            'student_no' => $row->student_no,
            'student_name' => $row->student_name,
            'student_email' => $row->student_email,
            'programme' => $row->programme,
            'student_photo' => $row->student_photo,
            'audit_trail' => $auditTrail,
        ];
    }

    /**
     * @param  array<int, object>  $rows
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function fetchEnrollmentLogsForPage(array $rows): array
    {
        if (! $this->hasEnrollmentStatusLogTable()) {
            return [];
        }

        $enrollmentIds = collect($rows)
            ->pluck('enrollment_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($enrollmentIds === []) {
            return [];
        }

        $logs = DB::table('enrollment_status_logs')
            ->leftJoin('users', 'enrollment_status_logs.performed_by', '=', 'users.id')
            ->select(
                'enrollment_status_logs.enrollment_id',
                'enrollment_status_logs.action',
                'enrollment_status_logs.from_status',
                'enrollment_status_logs.to_status',
                'enrollment_status_logs.reason',
                'enrollment_status_logs.created_at',
                'users.name as performer_name'
            )
            ->whereIn('enrollment_status_logs.enrollment_id', $enrollmentIds)
            ->orderByDesc('enrollment_status_logs.created_at')
            ->orderByDesc('enrollment_status_logs.id')
            ->get();

        return $logs
            ->groupBy(fn ($log) => (int) ($log->enrollment_id ?? 0))
            ->map(function ($group) {
                return $group->map(function ($log) {
                    $fromStatus = is_string($log->from_status) ? $log->from_status : null;
                    $toStatus = is_string($log->to_status) ? $log->to_status : null;

                    return [
                        'action' => (string) $log->action,
                        'label' => $this->enrollmentActionLabel((string) $log->action, $fromStatus, $toStatus),
                        'from_status' => $fromStatus,
                        'to_status' => $toStatus,
                        'reason' => $log->reason,
                        'performed_by' => $log->performer_name,
                        'created_at' => $log->created_at,
                    ];
                })->values()->all();
            })
            ->toArray();
    }

    private function hasEnrollmentStatusLogTable(): bool
    {
        static $exists = null;

        if ($exists === null) {
            $exists = Schema::hasTable('enrollment_status_logs');
        }

        return (bool) $exists;
    }

    private function enrollmentActionLabel(string $action, ?string $fromStatus, ?string $toStatus): string
    {
        return match ($action) {
            'request_submitted' => 'Enrollment request submitted',
            'request_resubmitted' => 'Enrollment request resubmitted',
            'approved' => 'Enrollment approved',
            'rejected' => 'Enrollment rejected',
            'withdrawal_requested' => 'Withdrawal requested',
            'withdrawal_approved' => 'Withdrawal approved',
            'withdrawal_rejected' => 'Withdrawal rejected',
            'request_blocked_conflict' => 'Request blocked by timetable conflict',
            'review_blocked_conflict' => 'Approval blocked by timetable conflict',
            default => $this->fallbackActionLabel($action, $fromStatus, $toStatus),
        };
    }

    private function fallbackActionLabel(string $action, ?string $fromStatus, ?string $toStatus): string
    {
        if ($fromStatus !== null && $toStatus !== null && $fromStatus !== $toStatus) {
            return sprintf('Status changed: %s -> %s', $fromStatus, $toStatus);
        }
        if ($toStatus !== null) {
            return sprintf('Status: %s', $toStatus);
        }

        return ucfirst(str_replace('_', ' ', $action));
    }
}
