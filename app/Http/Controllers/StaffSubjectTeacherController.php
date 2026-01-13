<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffSubjectTeacherController extends Controller
{
    /**
     * Show the form for assigning teachers to a subject.
     */
    public function edit(Subject $subject): Response
    {
        // Load the course relationship
        $subject->load('course');

        // Get all teachers (users with role 'teacher')
        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get currently assigned teacher IDs
        $assignedTeacherIds = $subject->teachers()->pluck('users.id')->toArray();

        return Inertia::render('Admin/Subjects/AssignTeachers', [
            'subject' => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'course_code' => $subject->course->course_code,
                'course_title' => $subject->course->title,
            ],
            'teachers' => $teachers,
            'assignedTeacherIds' => $assignedTeacherIds,
        ]);
    }

    /**
     * Update teacher assignments for a subject.
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => ['exists:users,id'],
        ]);

        // Sync teachers (this will add/remove as needed)
        $subject->teachers()->sync($data['teacher_ids']);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Teachers assigned to subject successfully.');
    }
}
