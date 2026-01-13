<?php

namespace App\Http\Controllers;

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
        $student = $user->student;

        if (!$student) {
            return Inertia::render('Student/Fees/Index', [
                'fees' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        $fees = Fee::where('student_id', $student->id)
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
            ->map(function ($fee) {
                return [
                    'id' => $fee->id,
                    'amount' => $fee->amount,
                    'description' => $fee->description,
                    'status' => $fee->status,
                    'due_date' => $fee->due_date->format('Y-m-d'),
                    'paid_date' => $fee->paid_date?->format('Y-m-d'),
                    'created_at' => $fee->created_at->format('Y-m-d'),
                ];
            });

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
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
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
        $fee->update(['status' => 'payment_pending']);

        return redirect()
            ->route('student.fees.index')
            ->with('success', "Payment confirmation submitted for fee of £{$fee->amount}. Waiting for admin approval.");
    }
}
