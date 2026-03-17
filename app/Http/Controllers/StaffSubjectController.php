<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Subjects\StoreSubjectRequest;
use App\Http\Requests\Staff\Subjects\UpdateSubjectRequest;
use App\Models\Course;
use App\Models\Subject;
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

class StaffSubjectController extends Controller
{
    /**
     * Display a listing of all subjects (staff admin view).
     */
    public function index(): Response
    {
        $subjects = Subject::query()
            ->with([
                'course:id,course_code,title',
                'teachers:id,name',
            ])
            ->withCount([
                'assignments',
                'assignments as published_assignments_count' => function ($query): void {
                    $query->where('status', 'published');
                },
            ])
            ->orderBy('subject_code')
            ->get([
                'id',
                'course_id',
                'subject_code',
                'title',
                'credits',
                'description',
                'photo',
                'attendance_threshold',
                'created_at',
                'updated_at',
            ])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'course_id' => $subject->course_id,
                    'course_code' => $subject->course?->course_code,
                    'course_title' => $subject->course?->title,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'credits' => $subject->credits,
                    'description' => $subject->description,
                    'photo' => $subject->photo,
                    'photo_thumb' => ImageService::tablePath($subject->photo),
                    'teacher_count' => $subject->teachers->count(),
                    'assignments_count' => (int) ($subject->assignments_count ?? 0),
                    'published_assignments_count' => (int) ($subject->published_assignments_count ?? 0),
                    'attendance_threshold' => $subject->attendance_threshold,
                    'teachers' => $subject->teachers->map(function ($teacher) {
                        return [
                            'id' => $teacher->id,
                            'name' => $teacher->name,
                        ];
                    }),
                    'created_at' => $subject->created_at,
                    'updated_at' => $subject->updated_at,
                ];
            });

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $subjects,
            'teachers' => User::query()
                ->where('role', 'teacher')
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->map(fn (User $teacher) => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(): Response
    {
        $courses = Course::orderBy('course_code')
            ->get(['id', 'course_code', 'title']);

        return Inertia::render('Admin/Subjects/Create', [
            'courses' => $courses,
            'globalThreshold' => AttendanceAlertSettings::lowThreshold(),
        ]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::store($request->file('photo'), 'subjects');
        }

        $subject = Subject::create($data);

        $this->notifySubjectManagement(
            'Subject created',
            sprintf(
                '%s created subject %s - %s.',
                $this->actorName(),
                $subject->subject_code,
                $subject->title
            ),
            route('admin.subjects.edit', $subject),
            [
                'subject_id' => $subject->id,
                'action' => 'created',
            ]
        );

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject): Response
    {
        $courses = Course::orderBy('course_code')
            ->get(['id', 'course_code', 'title']);

        return Inertia::render('Admin/Subjects/Edit', [
            'subject' => [
                'id' => $subject->id,
                'course_id' => $subject->course_id,
                'subject_code' => $subject->subject_code,
                'title' => $subject->title,
                'credits' => $subject->credits,
                'description' => $subject->description,
                'attendance_threshold' => $subject->attendance_threshold,
                'photo_url' => $subject->photo
                    ? asset('storage/'.$subject->photo)
                    : null,
            ],
            'courses' => $courses,
            'globalThreshold' => AttendanceAlertSettings::lowThreshold(),
        ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($subject->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'subjects');
        }

        $subject->update($data);

        $this->notifySubjectManagement(
            'Subject updated',
            sprintf(
                '%s updated subject %s - %s.',
                $this->actorName(),
                $subject->subject_code,
                $subject->title
            ),
            route('admin.subjects.edit', $subject),
            [
                'subject_id' => $subject->id,
                'action' => 'updated',
            ]
        );

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the photo from the specified subject.
     */
    public function removePhoto(Subject $subject): RedirectResponse
    {
        // Delete photo file if exists
        ImageService::delete($subject->photo);

        // Remove photo reference from database
        $subject->update(['photo' => null]);

        return redirect()
            ->route('admin.subjects.edit', $subject)
            ->with('success', 'Subject photo removed successfully.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $subjectId = $subject->id;
        $subjectCode = $subject->subject_code;
        $subjectTitle = $subject->title;

        // Delete photo file first to avoid orphaned storage files.
        ImageService::delete($subject->photo);

        $subject->delete();

        $this->notifySubjectManagement(
            'Subject deleted',
            sprintf(
                '%s deleted subject %s - %s.',
                $this->actorName(),
                $subjectCode,
                $subjectTitle
            ),
            route('admin.subjects.index'),
            [
                'subject_id' => $subjectId,
                'action' => 'deleted',
            ]
        );

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function notifySubjectManagement(
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
                new ManagementActivityNotification('subjects', $title, $message, $url, $meta)
            );
        } catch (\Throwable $e) {
            Log::warning('staff_subject_management_notification_failed', [
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
