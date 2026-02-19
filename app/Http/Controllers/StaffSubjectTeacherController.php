<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Subjects\UpdateSubjectTeachersRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
    public function update(UpdateSubjectTeachersRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();

        // Sync teachers (this will add/remove as needed)
        $subject->teachers()->sync($data['teacher_ids']);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Teachers assigned to subject successfully.');
    }

    /**
     * Bulk-assign teachers to multiple subjects.
     */
    public function bulkAssign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'distinct', 'exists:subjects,id'],
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => [
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ]);

        $subjectIds = collect($data['subject_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $teacherIds = User::query()
            ->where('role', 'teacher')
            ->whereIn('id', $data['teacher_ids'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($teacherIds->isEmpty()) {
            return back()->withErrors([
                'teacher_ids' => 'Please select at least one valid teacher.',
            ]);
        }

        $subjects = Subject::query()
            ->whereIn('id', $subjectIds->all())
            ->get(['id']);

        if ($subjects->isEmpty()) {
            return back()->withErrors([
                'subject_ids' => 'Please select at least one valid subject.',
            ]);
        }

        DB::transaction(function () use ($subjects, $teacherIds): void {
            foreach ($subjects as $subject) {
                // Add assignments without removing existing teachers.
                $subject->teachers()->syncWithoutDetaching($teacherIds->all());
            }
        });

        return back()->with(
            'success',
            sprintf(
                'Assigned %d teacher(s) to %d subject(s).',
                $teacherIds->count(),
                $subjects->count()
            )
        );
    }
}
