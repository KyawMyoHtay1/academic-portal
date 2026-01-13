<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StudentProfileController extends Controller
{
    /**
     * Display the authenticated student's profile.
     * Timebox 1: Student self-view only.
     */
    public function show(): Response
    {
        $user = Auth::user();
        // Refresh the student relationship to get latest data
        $student = $user->student()->first();

        // If student record doesn't exist yet, show a message
        if (!$student) {
            return Inertia::render('StudentProfile/Show', [
                'student' => null,
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        // Refresh the student model to ensure we have latest data
        $student->refresh();

        return Inertia::render('StudentProfile/Show', [
            'student' => [
                'id' => $student->id,
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'dob' => $student->dob,
                'email' => $student->email,
                'phone' => $student->phone,
                'programme' => $student->programme,
                'intake_year' => $student->intake_year,
                'photo_url' => $student->photo
                    ? asset('storage/' . $student->photo)
                    : null,
            ],
        ]);
    }

    /**
     * Update the authenticated student's profile (limited fields only).
     * Students can only update phone and address-like fields, not core academic data.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()
                ->route('student.profile.show')
                ->with('error', 'Student record not found.');
        }

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            // Add more editable fields here as needed (e.g., address)
            // Core fields like student_no, programme, intake_year are NOT editable by student
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            
            // Store new photo
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()
            ->route('student.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}

