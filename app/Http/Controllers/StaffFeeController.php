<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Notifications\FeeStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffFeeController extends Controller
{
    /**
     * Display a listing of all fees (staff admin view).
     */
    public function index(): Response
    {
        // Get late payments (due_date < today and status = pending)
        $latePayments = Fee::with('student')
            ->where('status', 'pending')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function (Fee $fee) {
                return [
                    'id' => $fee->id,
                    'student_no' => $fee->student->student_no,
                    'student_name' => $fee->student->full_name,
                    'amount' => $fee->amount,
                    'description' => $fee->description,
                    'due_date' => $fee->due_date->format('Y-m-d'),
                    'days_overdue' => now()->diffInDays($fee->due_date),
                ];
            });

        $fees = Fee::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function (Fee $fee) {
                $isLate = $fee->status === 'pending' && $fee->due_date < now();
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
                    'created_at' => $fee->created_at->format('Y-m-d'),
                    'is_late' => $isLate,
                    'days_overdue' => $isLate ? now()->diffInDays($fee->due_date) : null,
                ];
            });

        return Inertia::render('Admin/Fees/Index', [
            'fees' => $fees,
            'latePayments' => $latePayments,
            'latePaymentsCount' => $latePayments->count(),
        ]);
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create(): Response
    {
        $students = Student::orderBy('full_name')
            ->get(['id', 'student_no', 'full_name', 'photo']);

        return Inertia::render('Admin/Fees/Create', [
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created fee.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
        ]);

        $fee = Fee::create([
            ...$data,
            'status' => 'pending',
        ]);

        // Notify the student about the new fee
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
    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,payment_pending,paid'],
            'paid_date' => ['nullable', 'date', 'required_if:status,paid'],
        ]);

        // If status changed to paid and no paid_date, set to today
        if ($data['status'] === 'paid' && empty($data['paid_date'])) {
            $data['paid_date'] = now()->format('Y-m-d');
        }

        // If status changed to pending, clear paid_date
        if ($data['status'] === 'pending') {
            $data['paid_date'] = null;
        }

        $originalStatus = $fee->status;

        $fee->update($data);

        // Notify the student if the status changed
        if ($fee->status !== $originalStatus) {
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
        $fee->delete();

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    /**
     * Approve a payment confirmation (marks fee as paid).
     */
    public function approvePayment(Request $request, Fee $fee): RedirectResponse
    {
        if ($fee->status !== 'payment_pending') {
            return redirect()
                ->route('admin.fees.index')
                ->with('error', 'Fee payment confirmation not found or already processed.');
        }

        $fee->update([
            'status' => 'paid',
            'paid_date' => now()->format('Y-m-d'),
        ]);

        // Notify the student
        $student = $fee->student;
        if ($student && $student->user) {
            $student->user->notify(new FeeStatusUpdated($fee));
        }

        return redirect()
            ->route('admin.fees.index')
            ->with('success', "Payment confirmed for fee of £{$fee->amount}.");
    }

    /**
     * Reject a payment confirmation (reverts to pending).
     */
    public function rejectPayment(Request $request, Fee $fee): RedirectResponse
    {
        if ($fee->status !== 'payment_pending') {
            return redirect()
                ->route('admin.fees.index')
                ->with('error', 'Fee payment confirmation not found or already processed.');
        }

        $fee->update([
            'status' => 'pending',
            'paid_date' => null,
        ]);

        // Notify the student
        $student = $fee->student;
        if ($student && $student->user) {
            $student->user->notify(new FeeStatusUpdated($fee));
        }

        return redirect()
            ->route('admin.fees.index')
            ->with('success', "Payment confirmation rejected for fee of £{$fee->amount}.");
    }

    /**
     * Generate and download a receipt PDF for a paid fee.
     */
    public function receipt(Fee $fee)
    {
        // Only generate receipts for paid fees
        if ($fee->status !== 'paid') {
            return redirect()
                ->route('admin.fees.index')
                ->with('error', 'Receipt can only be generated for paid fees.');
        }

        $fee->load('student');

        $pdf = Pdf::loadView('fees.receipt', [
            'fee' => $fee,
            'student' => $fee->student,
            'receipt_number' => 'REC-' . str_pad((string) $fee->id, 6, '0', STR_PAD_LEFT),
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
        ]);

        $filename = 'receipt-' . $fee->student->student_no . '-' . $fee->id . '.pdf';

        return $pdf->download($filename);
    }
}
