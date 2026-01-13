<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(): Response
    {
        $students = Student::with('user')
            ->orderBy('student_no')
            ->paginate(10)
            ->through(function (Student $student) {
                return [
                    'id' => $student->id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->full_name,
                    'email' => $student->email,
                    'programme' => $student->programme,
                    'intake_year' => $student->intake_year,
                    'photo' => $student->photo,
                ];
            });

        return Inertia::render('Students/Index', [
            'students' => $students,
        ]);
    }

    public function create(): Response
    {
        // Get users who don't have a student record yet and have role 'student'
        $users = User::where('role', 'student')
            ->whereDoesntHave('student')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Students/Create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:students,user_id'],
            'student_no' => ['required', 'string', 'max:50', 'unique:students,student_no'],
            'full_name' => ['required', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'programme' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        Student::create($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('Students/Edit', [
            'student' => [
                'id' => $student->id,
                'student_no' => $student->student_no,
                'full_name' => $student->full_name,
                'dob' => $student->dob,
                'email' => $student->email,
                'phone' => $student->phone,
                'programme' => $student->programme,
                'intake_year' => $student->intake_year,
                'photo_url' => $student->photo
                    ? asset('storage/' . $student->photo)
                    : null,
            ],
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'student_no' => ['required', 'string', 'max:50', 'unique:students,student_no,' . $student->id],
            'full_name' => ['required', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email,' . $student->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'programme' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($student->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        $student->update($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the photo from the specified student.
     */
    public function removePhoto(Student $student): RedirectResponse
    {
        // Delete photo file if exists
        ImageService::delete($student->photo);

        // Remove photo reference from database
        $student->update(['photo' => null]);

        return redirect()
            ->route('students.edit', $student)
            ->with('success', 'Student photo removed successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
}


