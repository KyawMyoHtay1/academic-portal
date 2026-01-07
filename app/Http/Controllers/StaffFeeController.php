<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
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
        $fees = Fee::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function (Fee $fee) {
                return [
                    'id' => $fee->id,
                    'student_no' => $fee->student->student_no,
                    'student_name' => $fee->student->full_name,
                    'amount' => $fee->amount,
                    'description' => $fee->description,
                    'status' => $fee->status,
                    'due_date' => $fee->due_date->format('Y-m-d'),
                    'paid_date' => $fee->paid_date?->format('Y-m-d'),
                    'created_at' => $fee->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Admin/Fees/Index', [
            'fees' => $fees,
        ]);
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create(): Response
    {
        $students = Student::orderBy('full_name')
            ->get(['id', 'student_no', 'full_name']);

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

        Fee::create([
            ...$data,
            'status' => 'pending',
        ]);

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
            'status' => ['required', 'in:pending,paid'],
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

        $fee->update($data);

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
}
