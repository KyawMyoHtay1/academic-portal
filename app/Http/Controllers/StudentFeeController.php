<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentFeeResource;
use App\Models\Fee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentFeeController extends Controller
{
    /**
     * Display the authenticated student's fees.
     */
    public function index(): Response
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403, 'Unauthorized action.');
        }

        $student = $user->student;

        if (! $student) {
            return Inertia::render('Student/Fees/Index', [
                'fees' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        $fees = StudentFeeResource::collection(
            Fee::where('student_id', $student->id)
                ->orderBy('due_date', 'desc')
                ->get([
                    'id',
                    'amount',
                    'description',
                    'status',
                    'due_date',
                    'paid_date',
                    'created_at',
                ])
        )->resolve();

        return Inertia::render('Student/Fees/Index', [
            'fees' => $fees,
        ]);
    }

    /**
     * Submit payment confirmation for a fee.
     * Payment confirmation requires admin approval.
     */
    public function submitPayment(Request $request, Fee $fee): RedirectResponse
    {
        $this->authorize('submitPayment', $fee);

        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Student record not found.');
        }

        // Verify the fee belongs to the student
        if ($fee->student_id !== $student->id) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'You are not authorized to access this fee.');
        }

        // Check if fee is already paid or has pending payment confirmation
        if ($fee->status === 'paid') {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'This fee has already been marked as paid.');
        }

        if ($fee->status === 'payment_pending') {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'You already have a pending payment confirmation for this fee.');
        }

        // Update fee status to payment_pending
        $fee->markAsPaymentPending($fee->payment_intent_id);

        return redirect()
            ->route('student.fees.index')
            ->with('success', "Payment confirmation submitted for fee of £{$fee->amount}. Waiting for admin approval.");
    }
}
