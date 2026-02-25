<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Courses\UpdateCourseTeachersRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use App\Notifications\ManagementActivityNotification;

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
        $existingTeacherIds = $course->teachers()->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $newTeacherIds = collect($data['teacher_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Sync teachers (this will add/remove as needed)
        $course->teachers()->sync($newTeacherIds);

        $addedTeacherIds = array_values(array_diff($newTeacherIds, $existingTeacherIds));
        $removedTeacherIds = array_values(array_diff($existingTeacherIds, $newTeacherIds));

        $this->notifyTeachersAssignmentChanges($course, $addedTeacherIds, $removedTeacherIds);
        $this->notifyStaffAssignmentSummary($course, count($addedTeacherIds), count($removedTeacherIds));

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Teachers assigned to course successfully.');
    }

    /**
     * @param  array<int, int>  $addedTeacherIds
     * @param  array<int, int>  $removedTeacherIds
     */
    private function notifyTeachersAssignmentChanges(Course $course, array $addedTeacherIds, array $removedTeacherIds): void
    {
        if ($addedTeacherIds !== []) {
            $addedTeachers = User::query()
                ->whereIn('id', $addedTeacherIds)
                ->where('role', 'teacher')
                ->get();

            foreach ($addedTeachers as $teacher) {
                try {
                    $teacher->notify(new ManagementActivityNotification(
                        'courses',
                        'Teaching assignment added',
                        sprintf(
                            'You were assigned to course %s - %s.',
                            $course->course_code,
                            $course->title
                        ),
                        route('teacher.courses.index'),
                        [
                            'course_id' => $course->id,
                            'action' => 'teacher_assigned',
                        ]
                    ));
                } catch (\Throwable $e) {
                    Log::warning('staff_course_teacher_added_notification_failed', [
                        'course_id' => $course->id,
                        'teacher_id' => $teacher->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if ($removedTeacherIds !== []) {
            $removedTeachers = User::query()
                ->whereIn('id', $removedTeacherIds)
                ->where('role', 'teacher')
                ->get();

            foreach ($removedTeachers as $teacher) {
                try {
                    $teacher->notify(new ManagementActivityNotification(
                        'courses',
                        'Teaching assignment removed',
                        sprintf(
                            'You were removed from course %s - %s.',
                            $course->course_code,
                            $course->title
                        ),
                        route('teacher.courses.index'),
                        [
                            'course_id' => $course->id,
                            'action' => 'teacher_unassigned',
                        ]
                    ));
                } catch (\Throwable $e) {
                    Log::warning('staff_course_teacher_removed_notification_failed', [
                        'course_id' => $course->id,
                        'teacher_id' => $teacher->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function notifyStaffAssignmentSummary(Course $course, int $addedCount, int $removedCount): void
    {
        if ($addedCount === 0 && $removedCount === 0) {
            return;
        }

        $recipients = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->get(['id', 'role', 'preferences']);

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send(
                $recipients,
                new ManagementActivityNotification(
                    'courses',
                    'Course teacher assignments updated',
                    sprintf(
                        '%s updated teachers for %s - %s (%d added, %d removed).',
                        $this->actorName(),
                        $course->course_code,
                        $course->title,
                        $addedCount,
                        $removedCount
                    ),
                    route('admin.courses.assign-teachers', $course),
                    [
                        'course_id' => $course->id,
                        'action' => 'teacher_assignments_updated',
                        'added_count' => $addedCount,
                        'removed_count' => $removedCount,
                    ]
                )
            );
        } catch (\Throwable $e) {
            Log::warning('staff_course_teacher_summary_notification_failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorName(): string
    {
        return Auth::user()?->name ?? 'System';
    }
}
