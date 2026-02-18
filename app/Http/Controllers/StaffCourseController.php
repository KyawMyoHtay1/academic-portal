<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Courses\StoreCourseRequest;
use App\Http\Requests\Staff\Courses\UpdateCourseRequest;
use App\Models\Course;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
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
                'created_at',
                'updated_at',
            ])
            ->withCount([
                'students as enrolled_students_count' => function ($query): void {
                    $query->whereIn('course_student.status', ['approved', 'withdrawal_pending']);
                },
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
        return Inertia::render('Admin/Courses/Create');
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

        Course::create($data);

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
                'photo_url' => $course->photo
                    ? asset('storage/'.$course->photo)
                    : null,
            ],
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

        ImageService::delete($course->photo);
        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
