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
            ->paginate(50)
            ->through(function (Student $student) {
                return [
                    'id' => $student->id,
                    'student_no' => $student->student_no,
                    'full_name' => $student->full_name,
                    'email' => $student->email,
                    'programme' => $student->programme,
                    'intake_year' => $student->intake_year,
                    'status' => $student->status,
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
            'programmes' => $this->getProgrammes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:students,user_id'],
            'student_no' => ['nullable', 'string', 'max:50', 'unique:students,student_no'],
            'full_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'programme' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'id_card' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'transcript' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'previous_institution' => ['required', 'string', 'max:255'],
            'previous_qualification' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,suspended,graduated'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        // Auto-generate student number if not provided
        $data['student_no'] = $data['student_no'] ?? $this->generateStudentNo();

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        if ($request->hasFile('id_card')) {
            $data['id_card'] = $this->storeDocument($request->file('id_card'), 'students/documents');
        }

        if ($request->hasFile('transcript')) {
            $data['transcript'] = $this->storeDocument($request->file('transcript'), 'students/documents');
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
                'gender' => $student->gender,
                'nationality' => $student->nationality,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
                'emergency_contact_name' => $student->emergency_contact_name,
                'emergency_contact_phone' => $student->emergency_contact_phone,
                'programme' => $student->programme,
                'intake_year' => $student->intake_year,
                'previous_institution' => $student->previous_institution,
                'previous_qualification' => $student->previous_qualification,
                'status' => $student->status ?? 'active',
                'notes' => $student->notes,
                'enrollment_date' => $student->enrollment_date,
                'photo_url' => $student->photo
                    ? asset('storage/' . $student->photo)
                    : null,
                'id_card_url' => $student->id_card
                    ? asset('storage/' . $student->id_card)
                    : null,
                'transcript_url' => $student->transcript
                    ? asset('storage/' . $student->transcript)
                    : null,
            ],
            'programmes' => $this->getProgrammes(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'student_no' => ['required', 'string', 'max:50', 'unique:students,student_no,' . $student->id],
            'full_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email,' . $student->id],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\\-() ]+$/'],
            'programme' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'id_card' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'transcript' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
            'previous_institution' => ['required', 'string', 'max:255'],
            'previous_qualification' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,suspended,graduated'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($student->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        if ($request->hasFile('id_card')) {
            // Delete old document if it exists
            $this->deleteDocument($student->id_card);

            $data['id_card'] = $this->storeDocument($request->file('id_card'), 'students/documents');
        }

        if ($request->hasFile('transcript')) {
            // Delete old document if it exists
            $this->deleteDocument($student->transcript);

            $data['transcript'] = $this->storeDocument($request->file('transcript'), 'students/documents');
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

    /**
     * Generate the next student number in the format STU0001, STU0002, ...
     */
    protected function generateStudentNo(): string
    {
        $latest = Student::orderByDesc('id')->value('student_no');

        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'STU' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the list of available programmes.
     *
     * NOTE: This is a fixed list for now. If the university later
     *       decides to manage programmes dynamically, this can be
     *       replaced with a Programme model + table.
     */
    protected function getProgrammes(): array
    {
        return [
            'BSc (Hons) Computing',
            'BSc Computer Science',
            'BSc Information Technology',
            'BSE Software Engineering',
            'BEng Software Engineering',
            'BBA Business Administration',
        ];
    }

    /**
     * Store a document file (PDF or image) in storage
     */
    protected function storeDocument($file, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $folder . '/' . uniqid() . '.' . $extension;

        // For PDFs, store directly without image processing
        if ($extension === 'pdf') {
            Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
        } else {
            // For images, use ImageService for optimization
            $filename = ImageService::store($file, $folder);
        }

        return $filename;
    }

    /**
     * Delete a document file from storage
     */
    protected function deleteDocument(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}


