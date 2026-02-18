<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Fees\StoreFeeRequest;
use App\Http\Requests\Staff\Fees\UpdateFeeRequest;
use App\Models\Fee;
use App\Models\Student;
use App\Notifications\FeePaymentReminder;
use App\Notifications\FeeStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING])
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
            'statusLogs' => function ($q) {
                $q->with('performer:id,name')
                    ->latest('created_at')
                    ->limit(10);
            },
        ]);

        $this->applyIndexFilters($query, $filters, $todayDate);

        $filteredSummary = [
            'count' => (int) (clone $query)->count(),
            'amount' => (float) (clone $query)->sum('amount'),
        ];

        $fees = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString()
            ->through(function (Fee $fee) use ($today) {
                $isLate = in_array($fee->status, [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING], true)
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
                    'payment_processed_at' => $fee->payment_processed_at?->toDateTimeString(),
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

        if ($data['status'] === Fee::STATUS_PENDING) {
            $data['paid_date'] = null;
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

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    /**
     * Approve a payment confirmation (marks fee as paid).
     */
    public function approvePayment(Fee $fee): RedirectResponse
    {
        $this->authorize('approvePayment', $fee);

        if ($fee->status !== Fee::STATUS_PAYMENT_PENDING) {
            return redirect()
                ->route('admin.fees.index')
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

        return redirect()
            ->route('admin.fees.index')
            ->with('success', "Payment confirmed for fee of GBP {$fee->amount}.");
    }

    /**
     * Reject a payment confirmation (reverts to pending).
     */
    public function rejectPayment(Fee $fee): RedirectResponse
    {
        $this->authorize('rejectPayment', $fee);

        if ($fee->status !== Fee::STATUS_PAYMENT_PENDING) {
            return redirect()
                ->route('admin.fees.index')
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

        return redirect()
            ->route('admin.fees.index')
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
     * @return array<string, mixed>
     */
    private function resolveFilters(Request $request): array
    {
        $status = (string) $request->input('status', 'all');
        $allowedStatuses = ['all', Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING, Fee::STATUS_PAID];
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
            $query->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING])
                ->whereDate('due_date', '<', $todayDate);
        }

        if ($filters['due_bucket'] !== 'all') {
            $query->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING])
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
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING])
            ->sum('amount');

        $overdueBase = Fee::query()
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PAYMENT_PENDING])
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
                    'created_at' => $log->created_at?->format('Y-m-d H:i'),
                ];
            })
            ->all();

        if ($timeline !== []) {
            return $timeline;
        }

        return [[
            'id' => null,
            'action' => 'snapshot',
            'label' => 'Current status snapshot',
            'from_status' => null,
            'to_status' => $fee->status,
            'performed_by' => $fee->processor?->name,
            'note' => null,
            'created_at' => $fee->updated_at?->format('Y-m-d H:i'),
        ]];
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
}
