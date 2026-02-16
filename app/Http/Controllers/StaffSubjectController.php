<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects,subject_code'],
            'title' => ['required', 'string', 'max:255'],
            'credits' => ['nullable', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

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
                'photo_url' => $subject->photo
                    ? asset('storage/'.$subject->photo)
                    : null,
            ],
            'courses' => $courses,
        ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects,subject_code,'.$subject->id],
            'title' => ['required', 'string', 'max:255'],
            'credits' => ['nullable', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

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
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
