<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Subjects\UpdateSubjectTeachersRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use App\Notifications\ManagementActivityNotification;

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
        $existingTeacherIds = $subject->teachers()->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $newTeacherIds = collect($data['teacher_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Sync teachers (this will add/remove as needed)
        $subject->teachers()->sync($newTeacherIds);

        $addedTeacherIds = array_values(array_diff($newTeacherIds, $existingTeacherIds));
        $removedTeacherIds = array_values(array_diff($existingTeacherIds, $newTeacherIds));

        $this->notifyTeachersSubjectAssignmentChanges($subject, $addedTeacherIds, $removedTeacherIds);
        $this->notifyStaffSubjectAssignmentSummary($subject, count($addedTeacherIds), count($removedTeacherIds));

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

        $this->notifyTeachersBulkSubjectAssignment($teacherIds->all(), $subjects->count());
        $this->notifyStaffBulkSubjectAssignment($teacherIds->count(), $subjects->count());

        return back()->with(
            'success',
            sprintf(
                'Assigned %d teacher(s) to %d subject(s).',
                $teacherIds->count(),
                $subjects->count()
            )
        );
    }

    /**
     * @param  array<int, int>  $addedTeacherIds
     * @param  array<int, int>  $removedTeacherIds
     */
    private function notifyTeachersSubjectAssignmentChanges(
        Subject $subject,
        array $addedTeacherIds,
        array $removedTeacherIds
    ): void {
        if ($addedTeacherIds !== []) {
            $addedTeachers = User::query()
                ->whereIn('id', $addedTeacherIds)
                ->where('role', 'teacher')
                ->get();

            foreach ($addedTeachers as $teacher) {
                try {
                    $teacher->notify(new ManagementActivityNotification(
                        'subjects',
                        'Subject assignment added',
                        sprintf(
                            'You were assigned to subject %s - %s.',
                            $subject->subject_code,
                            $subject->title
                        ),
                        route('teacher.courses.index'),
                        [
                            'subject_id' => $subject->id,
                            'action' => 'teacher_assigned',
                        ]
                    ));
                } catch (\Throwable $e) {
                    Log::warning('staff_subject_teacher_added_notification_failed', [
                        'subject_id' => $subject->id,
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
                        'subjects',
                        'Subject assignment removed',
                        sprintf(
                            'You were removed from subject %s - %s.',
                            $subject->subject_code,
                            $subject->title
                        ),
                        route('teacher.courses.index'),
                        [
                            'subject_id' => $subject->id,
                            'action' => 'teacher_unassigned',
                        ]
                    ));
                } catch (\Throwable $e) {
                    Log::warning('staff_subject_teacher_removed_notification_failed', [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function notifyStaffSubjectAssignmentSummary(Subject $subject, int $addedCount, int $removedCount): void
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
                    'subjects',
                    'Subject teacher assignments updated',
                    sprintf(
                        '%s updated teachers for %s - %s (%d added, %d removed).',
                        $this->actorName(),
                        $subject->subject_code,
                        $subject->title,
                        $addedCount,
                        $removedCount
                    ),
                    route('admin.subjects.assign-teachers', $subject),
                    [
                        'subject_id' => $subject->id,
                        'action' => 'teacher_assignments_updated',
                        'added_count' => $addedCount,
                        'removed_count' => $removedCount,
                    ]
                )
            );
        } catch (\Throwable $e) {
            Log::warning('staff_subject_teacher_summary_notification_failed', [
                'subject_id' => $subject->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $teacherIds
     */
    private function notifyTeachersBulkSubjectAssignment(array $teacherIds, int $subjectCount): void
    {
        if ($teacherIds === []) {
            return;
        }

        $teachers = User::query()
            ->whereIn('id', $teacherIds)
            ->where('role', 'teacher')
            ->get();

        foreach ($teachers as $teacher) {
            try {
                $teacher->notify(new ManagementActivityNotification(
                    'subjects',
                    'Bulk subject assignments updated',
                    sprintf(
                        'A bulk update targeted %d subject(s) for your teaching assignments.',
                        $subjectCount
                    ),
                    route('teacher.courses.index'),
                    [
                        'action' => 'bulk_teacher_assigned',
                        'subject_count' => $subjectCount,
                    ]
                ));
            } catch (\Throwable $e) {
                Log::warning('staff_subject_teacher_bulk_target_notification_failed', [
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function notifyStaffBulkSubjectAssignment(int $teacherCount, int $subjectCount): void
    {
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
                    'subjects',
                    'Bulk subject assignments updated',
                    sprintf(
                        '%s ran bulk teacher assignment for %d teacher(s) across %d subject(s).',
                        $this->actorName(),
                        $teacherCount,
                        $subjectCount
                    ),
                    route('admin.subjects.index'),
                    [
                        'action' => 'bulk_teacher_assignments_updated',
                        'teacher_count' => $teacherCount,
                        'subject_count' => $subjectCount,
                    ]
                )
            );
        } catch (\Throwable $e) {
            Log::warning('staff_subject_teacher_bulk_summary_notification_failed', [
                'teacher_count' => $teacherCount,
                'subject_count' => $subjectCount,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorName(): string
    {
        return Auth::user()?->name ?? 'System';
    }
}
