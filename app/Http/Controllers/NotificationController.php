<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * Display a list of the authenticated user's notifications.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) use ($user) {
                $rawType = (string) ($notification->data['type'] ?? 'info');
                $normalizedType = $rawType === 'grade_review_result' ? 'grade_review' : $rawType;

                return [
                    'id' => $notification->id,
                    'type' => $normalizedType,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'read_at' => $notification->read_at?->toIso8601String(),
                    'created_at' => $notification->created_at->toIso8601String(),
                    'url' => $notification->data['url'] ?? $this->resolveNotificationUrl($notification->data, $user),
                ];
            });

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveNotificationUrl(array $data, object $user): ?string
    {
        $type = (string) ($data['type'] ?? 'info');
        $role = $user->role ?? null;

        if ($type === 'grade') {
            if ($role === 'student') {
                return route('student.grades.index');
            }
            if ($role === 'teacher' && ! empty($data['subject_id'])) {
                return route('teacher.grades.show', $data['subject_id']);
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                if (! empty($data['subject_id'])) {
                    return route('admin.grades.show', $data['subject_id']);
                }

                return route('admin.grades.index');
            }
        }

        if ($type === 'grade_review' || $type === 'grade_review_result') {
            if ($role === 'teacher' && ! empty($data['subject_id'])) {
                return route('teacher.grades.show', $data['subject_id']);
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                if (! empty($data['subject_id'])) {
                    return route('admin.grades.show', $data['subject_id']);
                }

                return route('admin.grades.index');
            }
        }

        if ($type === 'fee') {
            if ($role === 'student') {
                return route('student.fees.index');
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                return route('admin.fees.index');
            }
        }

        if ($type === 'attendance') {
            if ($role === 'student') {
                return route('student.attendance.index');
            }
            if ($role === 'teacher' && ! empty($data['subject_id'])) {
                return route('teacher.attendance.show', $data['subject_id']);
            }
            if ($role === 'teacher') {
                return route('teacher.attendance.index');
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                return route('admin.attendance.report', [
                    'course_id' => $data['course_id'] ?? null,
                    'subject_id' => $data['subject_id'] ?? null,
                ]);
            }
        }

        if ($type === 'timetable') {
            if ($role === 'student') {
                return route('student.timetable.index');
            }
            if ($role === 'teacher') {
                return route('teacher.timetable.index');
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                return route('admin.timetables.index', [
                    'course_id' => $data['course_id'] ?? null,
                ]);
            }
        }

        if ($type === 'announcement') {
            return route('announcements.index');
        }

        if ($type === 'assignment') {
            if ($role === 'student') {
                if (! empty($data['assignment_id'])) {
                    return route('student.assignments.show', $data['assignment_id']);
                }

                return route('student.assignments.index');
            }

            if ($role === 'teacher') {
                if (! empty($data['subject_id'])) {
                    return route('teacher.assignments.show', $data['subject_id']);
                }

                return route('teacher.assignments.index');
            }
        }

        if ($type === 'enrollment') {
            if ($role === 'student') {
                return route('my-courses.index');
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                return route('admin.enrollments.index');
            }
        }

        if ($type === 'contact' && $role === 'staff') {
            return route('admin.contact-messages.index', ['status' => 'unread']);
        }

        if ($type === 'feedback' && $role === 'staff') {
            return route('admin.feedback-messages.index', ['status' => 'unread']);
        }

        if ($type === 'message') {
            if (! empty($data['sender_id'])) {
                return route('messages.index', ['with_user' => (int) $data['sender_id']]);
            }

            return route('messages.index');
        }

        if ($type === 'management' && in_array($role, ['staff', 'admin'], true)) {
            $module = (string) ($data['module'] ?? '');

            if ($module === 'users') {
                return route('admin.users.index');
            }

            if ($module === 'courses') {
                return route('admin.courses.index');
            }

            if ($module === 'subjects') {
                return route('admin.subjects.index');
            }
        }

        return null;
    }
}
