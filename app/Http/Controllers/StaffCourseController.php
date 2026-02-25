<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Courses\StoreCourseRequest;
use App\Http\Requests\Staff\Courses\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use App\Notifications\ManagementActivityNotification;
use App\Services\ImageService;
use App\Support\AttendanceAlertSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class StaffCourseController extends Controller
{
    /**
     * Display a listing of all courses (staff admin view).
     */
    public function index(): Response
    {
        $courses = Course::query()
            ->select([
                'id',
                'course_code',
                'title',
                'credits',
                'semester',
                'photo',
                'attendance_threshold',
                'created_at',
                'updated_at',
            ])
            ->withCount([
                'students as enrolled_students_count' => function ($query): void {
                    $query->whereIn('course_student.status', ['approved', 'withdrawal_pending']);
                },
                'subjects',
                'teachers',
            ])
            ->orderBy('course_code')
            ->get();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Courses/Create', [
            'globalThreshold' => AttendanceAlertSettings::lowThreshold(),
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::store($request->file('photo'), 'courses');
        }

        $course = Course::create($data);

        $this->notifyCourseManagement(
            'Course created',
            sprintf(
                '%s created course %s - %s.',
                $this->actorName(),
                $course->course_code,
                $course->title
            ),
            route('admin.courses.edit', $course),
            [
                'course_id' => $course->id,
                'action' => 'created',
            ]
        );

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): Response
    {
        return Inertia::render('Admin/Courses/Edit', [
            'course' => [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title,
                'credits' => $course->credits,
                'semester' => $course->semester,
                'attendance_threshold' => $course->attendance_threshold,
                'photo_url' => $course->photo
                    ? asset('storage/'.$course->photo)
                    : null,
            ],
            'globalThreshold' => AttendanceAlertSettings::lowThreshold(),
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($course->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'courses');
        }

        $course->update($data);

        $this->notifyCourseManagement(
            'Course updated',
            sprintf(
                '%s updated course %s - %s.',
                $this->actorName(),
                $course->course_code,
                $course->title
            ),
            route('admin.courses.edit', $course),
            [
                'course_id' => $course->id,
                'action' => 'updated',
            ]
        );

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the photo from the specified course.
     */
    public function removePhoto(Course $course): RedirectResponse
    {
        // Delete photo file if exists
        ImageService::delete($course->photo);

        // Remove photo reference from database
        $course->update(['photo' => null]);

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('success', 'Course photo removed successfully.');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        // Check if course has active enrollments only.
        $enrolledCount = $course->students()
            ->wherePivotIn('status', ['approved', 'withdrawal_pending'])
            ->count();

        if ($enrolledCount > 0) {
            return redirect()
                ->route('admin.courses.index')
                ->with('error', "Cannot delete course. {$enrolledCount} student(s) are currently enrolled.");
        }

        $courseId = $course->id;
        $courseCode = $course->course_code;
        $courseTitle = $course->title;

        ImageService::delete($course->photo);
        $course->delete();

        $this->notifyCourseManagement(
            'Course deleted',
            sprintf(
                '%s deleted course %s - %s.',
                $this->actorName(),
                $courseCode,
                $courseTitle
            ),
            route('admin.courses.index'),
            [
                'course_id' => $courseId,
                'action' => 'deleted',
            ]
        );

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function notifyCourseManagement(
        string $title,
        string $message,
        ?string $url = null,
        array $meta = []
    ): void {
        $recipients = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->get(['id', 'role', 'preferences']);

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send(
                $recipients,
                new ManagementActivityNotification('courses', $title, $message, $url, $meta)
            );
        } catch (\Throwable $e) {
            Log::warning('staff_course_management_notification_failed', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorName(): string
    {
        return Auth::user()?->name ?? 'System';
    }
}
