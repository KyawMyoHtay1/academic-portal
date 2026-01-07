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
}
