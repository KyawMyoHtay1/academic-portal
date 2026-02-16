<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Courses\UpdateCourseTeachersRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffCourseTeacherController extends Controller
{
    /**
     * Show the form for assigning teachers to a course.
     */
    public function edit(Course $course): Response
    {
        // Get all teachers (users with role 'teacher')
        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get currently assigned teacher IDs
        $assignedTeacherIds = $course->teachers()->pluck('users.id')->toArray();

        return Inertia::render('Admin/Courses/AssignTeachers', [
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
            ],
            'teachers' => $teachers,
            'assignedTeacherIds' => $assignedTeacherIds,
        ]);
    }

    /**
     * Update teacher assignments for a course.
     */
    public function update(UpdateCourseTeachersRequest $request, Course $course): RedirectResponse
    {
        $data = $request->validated();

        // Sync teachers (this will add/remove as needed)
        $course->teachers()->sync($data['teacher_ids']);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Teachers assigned to course successfully.');
    }
}
