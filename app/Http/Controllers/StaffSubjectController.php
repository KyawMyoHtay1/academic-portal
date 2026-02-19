<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Subjects\StoreSubjectRequest;
use App\Http\Requests\Staff\Subjects\UpdateSubjectRequest;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffSubjectController extends Controller
{
    /**
     * Display a listing of all subjects (staff admin view).
     */
    public function index(): Response
    {
        $subjects = Subject::with(['course', 'teachers'])
            ->orderBy('subject_code')
            ->get([
                'id',
                'course_id',
                'subject_code',
                'title',
                'credits',
                'description',
                'photo',
                'created_at',
                'updated_at',
            ])
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'course_id' => $subject->course_id,
                    'course_code' => $subject->course->course_code,
                    'course_title' => $subject->course->title,
                    'subject_code' => $subject->subject_code,
                    'title' => $subject->title,
                    'credits' => $subject->credits,
                    'description' => $subject->description,
                    'photo' => $subject->photo,
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
            'globalThreshold' => (float) config('attendance_alerts.low_threshold', 75),
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

        Subject::create($data);

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
            'globalThreshold' => (float) config('attendance_alerts.low_threshold', 75),
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
        // Delete photo file first to avoid orphaned storage files.
        ImageService::delete($subject->photo);

        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
