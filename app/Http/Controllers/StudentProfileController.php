<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentProfile\UpdateStudentProfileRequest;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        if (! $student) {
            return Inertia::render('StudentProfile/Show', [
                'student' => null,
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        // Refresh the student model to ensure we have latest data
        $student->refresh();

        // Calculate GPA
        $gpa = $student->calculateGPA();

        return Inertia::render('StudentProfile/Show', [
            'student' => [
                'id' => $student->id,
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'dob' => $student->dob,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
                'programme' => $student->programme,
                'intake_year' => $student->intake_year,
                'gpa' => $gpa,
                'photo_url' => $student->photo
                    ? asset('storage/'.$student->photo)
                    : null,
            ],
        ]);
    }

    /**
     * Update the authenticated student's profile (limited fields only).
     * Students can only update phone and address-like fields, not core academic data.
     */
    public function update(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()
                ->route('student.profile.show')
                ->with('error', 'Student record not found.');
        }

        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            ImageService::delete($student->photo);

            // Store new optimized photo
            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        $student->update($data);

        return redirect()
            ->route('student.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the authenticated student's profile photo.
     */
    public function removePhoto(): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()
                ->route('student.profile.show')
                ->with('error', 'Student record not found.');
        }

        // Delete photo file if exists
        ImageService::delete($student->photo);

        // Remove photo reference from database
        $student->update(['photo' => null]);

        return redirect()
            ->route('student.profile.show')
            ->with('success', 'Profile photo removed successfully.');
    }
}
