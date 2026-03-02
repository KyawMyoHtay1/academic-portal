<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentFeeResource;
use App\Models\Fee;
use App\Models\User;
use App\Notifications\FeePaymentPendingReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
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

        if ($fee->student_id !== $student->id) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'You are not authorized to access this fee.');
        }

        if ($fee->status === Fee::STATUS_PAID) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Action blocked: this fee is already marked as paid, so another payment confirmation cannot be submitted.');
        }

        if ($fee->status === Fee::STATUS_PAYMENT_PENDING) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Action blocked: a payment confirmation for this fee is already pending admin review.');
        }

        $fee->markAsPaymentPending(
            $fee->payment_intent_id,
            Auth::id(),
            'student_submitted_payment',
            'Student submitted payment confirmation.'
        );

        $reviewers = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->get(['id', 'role', 'preferences']);

        if ($reviewers->isNotEmpty()) {
            $fee->loadMissing('student:id,student_no,full_name');
            Notification::send($reviewers, new FeePaymentPendingReview($fee, 'student_submission'));
        }

        return redirect()
            ->route('student.fees.index')
            ->with('success', "Payment confirmation submitted for fee of GBP {$fee->amount}. Waiting for admin approval.");
    }
}
