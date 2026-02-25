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
                    'read_at' => $notification->read_at?->format('Y-m-d H:i'),
                    'created_at' => $notification->created_at->format('Y-m-d H:i'),
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
    public function markAsRead(string $notificationId): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->firstOrFail();
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

        if ($type === 'enrollment') {
            if ($role === 'student') {
                return route('my-courses.index');
            }
            if (in_array($role, ['staff', 'admin'], true)) {
                return route('admin.enrollments.index');
            }
        }

        if ($type === 'message') {
            if (! empty($data['sender_id'])) {
                return route('messages.index', ['with_user' => (int) $data['sender_id']]);
            }

            return route('messages.index');
        }

        return null;
    }
}
