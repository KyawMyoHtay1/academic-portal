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
     * Mark a fee as paid (simplified payment flow).
     */
    public function pay(Request $request, Fee $fee): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'Student record not found.');
        }

        // Verify fee belongs to this student
        if ($fee->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if already paid
        if ($fee->status === 'paid') {
            return redirect()
                ->route('student.fees.index')
                ->with('error', 'This fee has already been paid.');
        }

        // Mark as paid
        $fee->update([
            'status' => 'paid',
            'paid_date' => now()->format('Y-m-d'),
        ]);

        return redirect()
            ->route('student.fees.index')
            ->with('success', 'Fee marked as paid successfully.');
    }
}
