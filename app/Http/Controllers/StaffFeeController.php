<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Fees\StoreFeeRequest;
use App\Http\Requests\Staff\Fees\UpdateFeeRequest;
use App\Models\Fee;
use App\Models\Student;
use App\Notifications\FeePaymentReminder;
use App\Notifications\FeeStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffFeeController extends Controller
{
    /**
     * Display a listing of all fees (staff admin view).
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Fee::class);

        $filters = $this->resolveFilters($request);
        $today = now()->startOfDay();
        $todayDate = $today->toDateString();

        $latePayments = Fee::with('student')
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
            ->whereDate('due_date', '<', $todayDate)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function (Fee $fee) use ($today) {
                return [
                    'id' => $fee->id,
                    'student_no' => $fee->student->student_no,
                    'student_name' => $fee->student->full_name,
                    'amount' => $fee->amount,
                    'description' => $fee->description,
                    'due_date' => $fee->due_date->format('Y-m-d'),
                    'days_overdue' => $fee->due_date->diffInDays($today),
                ];
            });

        $query = Fee::with([
            'student',
            'processor:id,name',
        ]);

        $this->applyIndexFilters($query, $filters, $todayDate);

        $filteredSummary = [
            'count' => (int) (clone $query)->count(),
            'amount' => (float) (clone $query)->sum('amount'),
        ];

        $fees = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        if ($this->hasFeeStatusLogTable()) {
            try {
                $fees->load([
                    'statusLogs' => function ($q) {
                        $q->with('performer:id,name')
                            ->latest('created_at')
                            ->limit(10);
                    },
                ]);
            } catch (QueryException $e) {
                if (! $this->isMissingFeeStatusLogTableException($e)) {
                    throw $e;
                }
            }
        }

        $fees = $fees->through(function (Fee $fee) use ($today) {
            $isLate = in_array($fee->status, [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED], true)
                && $fee->due_date < $today;

            return [
                'id' => $fee->id,
                'student_no' => $fee->student->student_no,
                'student_name' => $fee->student->full_name,
                'student_photo' => $fee->student->photo,
                'amount' => $fee->amount,
                'description' => $fee->description,
                'status' => $fee->status,
                'due_date' => $fee->due_date->format('Y-m-d'),
                'paid_date' => $fee->paid_date?->format('Y-m-d'),
                'processed_by' => $fee->processor?->name,
                'payment_processed_at' => $fee->payment_processed_at?->toIso8601String(),
                'created_at' => $fee->created_at->format('Y-m-d'),
                'is_late' => $isLate,
                'days_overdue' => $isLate ? $fee->due_date->diffInDays($today) : null,
                'timeline' => $this->mapFeeTimeline($fee),
            ];
        });

        return Inertia::render('Admin/Fees/Index', [
            'fees' => $fees,
            'filters' => $filters,
            'latePayments' => $latePayments,
            'latePaymentsCount' => $latePayments->count(),
            'financeSummary' => $this->buildFinanceSummary($todayDate),
            'filteredSummary' => $filteredSummary,
        ]);
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create(): Response
    {
        $this->authorize('create', Fee::class);

        $students = Student::orderBy('full_name')
            ->get(['id', 'student_no', 'full_name', 'photo']);

        return Inertia::render('Admin/Fees/Create', [
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created fee.
     */
    public function store(StoreFeeRequest $request): RedirectResponse
    {
        $this->authorize('create', Fee::class);

        $data = $request->validated();

        $fee = Fee::create([
            ...$data,
            'status' => Fee::STATUS_PENDING,
        ]);

        $fee->logStatusChange(
            null,
            Fee::STATUS_PENDING,
            'created',
            Auth::id(),
            'Fee created by staff.'
        );

        $student = Student::find($fee->student_id);
        if ($student && $student->user) {
            $student->user->notify(new FeeStatusUpdated($fee));
        }

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee created successfully.');
    }

    /**
     * Show the form for editing the specified fee.
     */
    public function edit(Fee $fee): Response
    {
        $this->authorize('update', $fee);

        $students = Student::orderBy('full_name')
            ->get(['id', 'student_no', 'full_name']);

        return Inertia::render('Admin/Fees/Edit', [
            'fee' => [
                'id' => $fee->id,
                'student_id' => $fee->student_id,
                'amount' => $fee->amount,
                'description' => $fee->description,
                'status' => $fee->status,
                'due_date' => $fee->due_date->format('Y-m-d'),
                'paid_date' => $fee->paid_date?->format('Y-m-d'),
            ],
            'students' => $students,
        ]);
    }

    /**
     * Update the specified fee.
     */
    public function update(UpdateFeeRequest $request, Fee $fee): RedirectResponse
    {
        $this->authorize('update', $fee);

        $data = $request->validated();

        if ($data['status'] === Fee::STATUS_PAID && empty($data['paid_date'])) {
            $data['paid_date'] = now()->format('Y-m-d');
        }

        if (in_array($data['status'], [Fee::STATUS_PENDING, Fee::STATUS_FAILED], true)) {
            $data['paid_date'] = null;
            $data['payment_method'] = null;
            $data['payment_intent_id'] = null;
            $data['payment_processed_at'] = null;
            $data['processed_by'] = null;
        }

        if ($data['status'] === Fee::STATUS_PAID) {
            $data['payment_processed_at'] = $data['payment_processed_at'] ?? now();
            $data['processed_by'] = Auth::id();
        }

        $originalStatus = $fee->status;

        $fee->update($data);

        if ($fee->status !== $originalStatus) {
            $fee->logStatusChange(
                $originalStatus,
                $fee->status,
                'staff_status_updated',
                Auth::id(),
                'Status updated from fee edit screen.'
            );

            $student = Student::find($fee->student_id);
            if ($student && $student->user) {
                $student->user->notify(new FeeStatusUpdated($fee));
            }
        }

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee updated successfully.');
    }

    /**
     * Remove the specified fee.
     */
    public function destroy(Fee $fee): RedirectResponse
    {
        $this->authorize('delete', $fee);

        $fee->delete();

        return back()
            ->with('success', 'Fee deleted successfully.');
    }

    /**
     * Approve a payment confirmation (marks fee as paid).
     */
    public function approvePayment(Fee $fee): RedirectResponse
    {
        $this->authorize('approvePayment', $fee);

        if ($fee->status !== Fee::STATUS_PAYMENT_PENDING) {
            return back()
                ->with('error', 'Fee payment confirmation not found or already processed.');
        }

        $fee->markAsPaid(
            $fee->payment_method,
            $fee->payment_intent_id,
            Auth::id(),
            'payment_approved',
            'Payment confirmation approved by staff.'
        );

        $student = $fee->student;
        if ($student && $student->user) {
            $student->user->notify(new FeeStatusUpdated($fee));
        }

        return back()
            ->with('success', "Payment confirmed for fee of GBP {$fee->amount}.");
    }

    /**
     * Reject a payment confirmation (reverts to pending).
     */
    public function rejectPayment(Fee $fee): RedirectResponse
    {
        $this->authorize('rejectPayment', $fee);

        if ($fee->status !== Fee::STATUS_PAYMENT_PENDING) {
            return back()
                ->with('error', 'Fee payment confirmation not found or already processed.');
        }

        $fee->markAsPending(
            Auth::id(),
            'payment_rejected',
            'Payment confirmation rejected by staff.'
        );

        $student = $fee->student;
        if ($student && $student->user) {
            $student->user->notify(new FeeStatusUpdated($fee));
        }

        return back()
            ->with('success', "Payment confirmation rejected for fee of GBP {$fee->amount}.");
    }

    /**
     * Send an overdue reminder to the student for this fee.
     */
    public function sendReminder(Fee $fee): RedirectResponse
    {
        $this->authorize('update', $fee);

        if ($fee->status === Fee::STATUS_PAID) {
            return back()->with('info', 'Cannot send reminder for a paid fee.');
        }

        if ($fee->due_date >= now()->startOfDay()) {
            return back()->with('info', 'Reminder is available only for overdue fees.');
        }

        $daysOverdue = $fee->due_date->diffInDays(now()->startOfDay());

        $student = $fee->student;
        if (! $student || ! $student->user) {
            return back()->with('error', 'Student user account not found for this fee.');
        }

        $student->user->notify(new FeePaymentReminder($fee, $daysOverdue));

        $fee->logStatusChange(
            $fee->status,
            $fee->status,
            'reminder_sent',
            Auth::id(),
            'Overdue reminder sent to student.',
            ['days_overdue' => $daysOverdue]
        );

        return back()->with('success', 'Overdue reminder sent successfully.');
    }

    /**
     * Send reminders for all overdue fees in the current filtered result.
     */
    public function sendOverdueReminders(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Fee::class);

        $today = now()->startOfDay();
        $todayDate = $today->toDateString();
        $filters = $this->resolveFilters($request);

        $query = Fee::query()->with('student.user');
        $this->applyIndexFilters($query, $filters, $todayDate);

        $query->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
            ->whereDate('due_date', '<', $todayDate);

        $feeIds = $request->input('fee_ids');
        if (is_array($feeIds) && $feeIds !== []) {
            $normalizedIds = collect($feeIds)
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($normalizedIds !== []) {
                $query->whereIn('id', $normalizedIds);
            }
        }

        $fees = $query->get();

        if ($fees->isEmpty()) {
            return back()->with('info', 'No overdue fees found for reminders in the current filter.');
        }

        $sent = 0;
        $skipped = 0;

        foreach ($fees as $fee) {
            $this->authorize('update', $fee);

            $student = $fee->student;
            if (! $student || ! $student->user) {
                $skipped++;
                continue;
            }

            $daysOverdue = $fee->due_date->diffInDays($today);
            $student->user->notify(new FeePaymentReminder($fee, $daysOverdue));

            $fee->logStatusChange(
                $fee->status,
                $fee->status,
                'reminder_sent',
                Auth::id(),
                'Overdue reminder sent to student (bulk).',
                ['days_overdue' => $daysOverdue]
            );

            $sent++;
        }

        if ($sent === 0) {
            return back()->with('info', 'No reminders were sent. Student user accounts may be missing.');
        }

        $message = "Sent {$sent} overdue reminder(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} fee(s) without a linked student user.";
        }

        return back()->with('success', $message);
    }

    /**
     * Generate and download a receipt PDF for a paid fee.
     */
    public function receipt(Fee $fee)
    {
        $this->authorize('receipt', $fee);

        if ($fee->status !== Fee::STATUS_PAID) {
            return redirect()
                ->route('admin.fees.index')
                ->with('error', 'Receipt can only be generated for paid fees.');
        }

        $fee->load(['student', 'processor:id,name']);

        $pdf = Pdf::loadView('fees.receipt', [
            'fee' => $fee,
            'student' => $fee->student,
            'receipt_number' => 'REC-'.str_pad((string) $fee->id, 6, '0', STR_PAD_LEFT),
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
        ]);

        $filename = 'receipt-'.$fee->student->student_no.'-'.$fee->id.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export filtered fee ledger (including receipt references) as CSV/PDF.
     */
    public function export(Request $request, string $format): StreamedResponse|\Illuminate\Http\Response
    {
        $this->authorize('viewAny', Fee::class);

        $format = strtolower($format);
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404);
        }

        $filters = $this->resolveFilters($request);
        $todayDate = now()->toDateString();

        $query = Fee::with(['student', 'processor:id,name']);
        $this->applyIndexFilters($query, $filters, $todayDate);

        $rows = $query
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Fee $fee) => $this->mapFeeExportRow($fee));

        $timestamp = now()->format('Ymd_His');

        if ($format === 'csv') {
            $filename = "fee_ledger_{$timestamp}.csv";

            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [
                    'Fee ID',
                    'Receipt No',
                    'Student No',
                    'Student Name',
                    'Amount',
                    'Status',
                    'Due Date',
                    'Paid Date',
                    'Description',
                    'Processed By',
                    'Processed At',
                    'Created At',
                ]);

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row['fee_id'],
                        $row['receipt_no'],
                        $row['student_no'],
                        $row['student_name'],
                        $row['amount'],
                        $row['status'],
                        $row['due_date'],
                        $row['paid_date'],
                        $row['description'],
                        $row['processed_by'],
                        $row['processed_at'],
                        $row['created_at'],
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $pdf = Pdf::loadView('fees.report', [
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("fee_ledger_{$timestamp}.pdf");
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFilters(Request $request): array
    {
        $status = (string) $request->input('status', 'all');
        $allowedStatuses = ['all', Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED, Fee::STATUS_PAID];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $dueBucket = (string) $request->input('due_bucket', 'all');
        $allowedDueBuckets = ['all', '0_7', '8_30', '31_plus'];
        if (! in_array($dueBucket, $allowedDueBuckets, true)) {
            $dueBucket = 'all';
        }

        return [
            'status' => $status,
            'search' => trim((string) $request->input('search', '')),
            'overdue_only' => $request->boolean('overdue_only'),
            'due_bucket' => $dueBucket,
        ];
    }

    /**
     * @return array<string, string|int|float|null>
     */
    private function mapFeeExportRow(Fee $fee): array
    {
        return [
            'fee_id' => $fee->id,
            'receipt_no' => $fee->status === Fee::STATUS_PAID
                ? 'REC-'.str_pad((string) $fee->id, 6, '0', STR_PAD_LEFT)
                : null,
            'student_no' => $fee->student->student_no,
            'student_name' => $fee->student->full_name,
            'amount' => (float) $fee->amount,
            'status' => $fee->status,
            'due_date' => $fee->due_date?->format('Y-m-d'),
            'paid_date' => $fee->paid_date?->format('Y-m-d'),
            'description' => $fee->description,
            'processed_by' => $fee->processor?->name,
            'processed_at' => $fee->payment_processed_at?->format('Y-m-d H:i:s'),
            'created_at' => $fee->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param  Builder<Fee>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyIndexFilters(Builder $query, array $filters, string $todayDate): void
    {
        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', '%'.$search.'%')
                    ->orWhere('amount', 'like', '%'.$search.'%')
                    ->orWhereHas('student', function (Builder $sq) use ($search) {
                        $sq->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('student_no', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($filters['overdue_only']) {
            $query->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
                ->whereDate('due_date', '<', $todayDate);
        }

        if ($filters['due_bucket'] !== 'all') {
            $query->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
                ->whereDate('due_date', '<', $todayDate);

            if ($filters['due_bucket'] === '0_7') {
                $query->whereDate('due_date', '>=', now()->subDays(7)->toDateString());
            } elseif ($filters['due_bucket'] === '8_30') {
                $query->whereDate('due_date', '<=', now()->subDays(8)->toDateString())
                    ->whereDate('due_date', '>=', now()->subDays(30)->toDateString());
            } elseif ($filters['due_bucket'] === '31_plus') {
                $query->whereDate('due_date', '<=', now()->subDays(31)->toDateString());
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFinanceSummary(string $todayDate): array
    {
        $totalBilled = (float) Fee::query()->sum('amount');
        $totalCollected = (float) Fee::query()
            ->where('status', Fee::STATUS_PAID)
            ->sum('amount');
        $totalOutstanding = (float) Fee::query()
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
            ->sum('amount');

        $overdueBase = Fee::query()
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_FAILED])
            ->whereDate('due_date', '<', $todayDate);

        $bucket0to7Query = (clone $overdueBase)
            ->whereDate('due_date', '>=', now()->subDays(7)->toDateString());

        $bucket8to30Query = (clone $overdueBase)
            ->whereDate('due_date', '<=', now()->subDays(8)->toDateString())
            ->whereDate('due_date', '>=', now()->subDays(30)->toDateString());

        $bucket31plusQuery = (clone $overdueBase)
            ->whereDate('due_date', '<=', now()->subDays(31)->toDateString());

        return [
            'total_billed' => $totalBilled,
            'total_collected' => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'aging' => [
                '0_7' => [
                    'count' => (int) (clone $bucket0to7Query)->count(),
                    'amount' => (float) (clone $bucket0to7Query)->sum('amount'),
                ],
                '8_30' => [
                    'count' => (int) (clone $bucket8to30Query)->count(),
                    'amount' => (float) (clone $bucket8to30Query)->sum('amount'),
                ],
                '31_plus' => [
                    'count' => (int) (clone $bucket31plusQuery)->count(),
                    'amount' => (float) (clone $bucket31plusQuery)->sum('amount'),
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapFeeTimeline(Fee $fee): array
    {
        if (! $this->hasFeeStatusLogTable() || ! $fee->relationLoaded('statusLogs')) {
            return $this->snapshotTimeline($fee);
        }

        $timeline = $fee->statusLogs
            ->sortBy('created_at')
            ->values()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'label' => $this->statusActionLabel($log->action, $log->from_status, $log->to_status),
                    'from_status' => $log->from_status,
                    'to_status' => $log->to_status,
                    'performed_by' => $log->performer?->name,
                    'note' => $log->note,
                    'created_at' => $log->created_at?->toIso8601String(),
                ];
            })
            ->all();

        return $timeline !== [] ? $timeline : $this->snapshotTimeline($fee);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function snapshotTimeline(Fee $fee): array
    {
        return [[
            'id' => null,
            'action' => 'snapshot',
            'label' => 'Current status snapshot',
            'from_status' => null,
            'to_status' => $fee->status,
            'performed_by' => $fee->processor?->name,
            'note' => null,
            'created_at' => $fee->updated_at?->toIso8601String(),
        ]];
    }

    private function hasFeeStatusLogTable(): bool
    {
        static $exists = null;

        if ($exists === null) {
            try {
                $exists = Schema::hasTable('fee_status_logs');
            } catch (\Throwable) {
                $exists = false;
            }
        }

        return (bool) $exists;
    }

    private function statusActionLabel(string $action, ?string $fromStatus, ?string $toStatus): string
    {
        if ($action === 'created') {
            return 'Fee created';
        }
        if ($action === 'payment_approved') {
            return 'Payment approved by staff';
        }
        if ($action === 'payment_rejected') {
            return 'Payment rejected by staff';
        }
        if ($action === 'payment_failed') {
            return 'Payment failed';
        }
        if ($action === 'checkout_cancelled') {
            return 'Checkout cancelled';
        }
        if ($action === 'reminder_sent') {
            return 'Overdue reminder sent';
        }

        if ($fromStatus && $toStatus) {
            return sprintf('Status changed: %s -> %s', $fromStatus, $toStatus);
        }

        if ($toStatus) {
            return sprintf('Status set to %s', $toStatus);
        }

        return ucfirst(str_replace('_', ' ', $action));
    }

    private function isMissingFeeStatusLogTableException(QueryException $e): bool
    {
        $sqlState = (string) $e->getCode();
        $message = strtolower($e->getMessage());

        return $sqlState === '42S02' && str_contains($message, 'fee_status_logs');
    }
}
