<?php

namespace App\Http\Controllers;

use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(): Response
    {
        $filters = request()->only(['search', 'programme', 'intake_year', 'status', 'sort_by', 'sort_dir']);
        $search = trim((string) ($filters['search'] ?? ''));
        $programme = trim((string) ($filters['programme'] ?? 'all'));
        $intakeYear = trim((string) ($filters['intake_year'] ?? 'all'));
        $status = trim((string) ($filters['status'] ?? 'all'));

        $allowedSorts = ['student_no', 'full_name', 'programme', 'intake_year', 'status'];
        $requestedSortBy = (string) ($filters['sort_by'] ?? 'student_no');
        $sortBy = in_array($requestedSortBy, $allowedSorts, true)
            ? $requestedSortBy
            : 'student_no';
        $sortDir = ($filters['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $studentsQuery = Student::query()->with('user');

        if ($search !== '') {
            $studentsQuery->where(function ($query) use ($search): void {
                $query->where('student_no', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('programme', 'like', "%{$search}%");
            });
        }

        if ($programme !== '' && $programme !== 'all') {
            $studentsQuery->where('programme', $programme);
        }

        if ($intakeYear !== '' && $intakeYear !== 'all') {
            $studentsQuery->where('intake_year', $intakeYear);
        }

        if ($status !== '' && $status !== 'all') {
            $studentsQuery->where('status', $status);
        }

        $students = $studentsQuery
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString()
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

        $programmes = Student::query()
            ->whereNotNull('programme')
            ->select('programme')
            ->distinct()
            ->orderBy('programme')
            ->pluck('programme')
            ->values();

        $intakeYears = Student::query()
            ->whereNotNull('intake_year')
            ->select('intake_year')
            ->distinct()
            ->orderByDesc('intake_year')
            ->pluck('intake_year')
            ->values();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'filters' => [
                'search' => $search,
                'programme' => $programme,
                'intake_year' => $intakeYear,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
            'filterOptions' => [
                'programmes' => $programmes,
                'intakeYears' => $intakeYears,
                'statuses' => ['active', 'suspended', 'graduated'],
            ],
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

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::store($request->file('photo'), 'students');
        }

        if ($request->hasFile('id_card')) {
            $data['id_card'] = $this->storeDocument($request->file('id_card'), 'students/documents');
        }

        if ($request->hasFile('transcript')) {
            $data['transcript'] = $this->storeDocument($request->file('transcript'), 'students/documents');
        }

        $userProvidedStudentNo = filled($data['student_no'] ?? null);
        $maxAttempts = $userProvidedStudentNo ? 1 : 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::transaction(function () use ($userProvidedStudentNo, &$data): void {
                    if (! $userProvidedStudentNo) {
                        // Generate inside transaction to reduce race conditions.
                        $data['student_no'] = $this->generateStudentNo(lockForUpdate: true);
                    }

                    Student::create($data);
                });

                return redirect()
                    ->route('students.index')
                    ->with('success', 'Student created successfully.');
            } catch (QueryException $e) {
                if ($this->isDuplicateUserIdException($e)) {
                    $this->cleanupUploadedStudentFiles($data);

                    return back()
                        ->withErrors([
                            'user_id' => 'This user already has a student profile.',
                        ])
                        ->withInput();
                }

                if ($this->isDuplicateStudentNoException($e)) {
                    if (! $userProvidedStudentNo && $attempt < $maxAttempts) {
                        continue;
                    }

                    $this->cleanupUploadedStudentFiles($data);

                    return back()
                        ->withErrors([
                            'student_no' => 'This student number is already in use. Please try again.',
                        ])
                        ->withInput();
                }

                throw $e;
            }
        }

        return redirect()
            ->back()
            ->withErrors([
                'student_no' => 'Unable to generate a unique student number. Please try again.',
            ])
            ->withInput();
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
                    ? asset('storage/'.$student->photo)
                    : null,
                'id_card_url' => $student->id_card
                    ? asset('storage/'.$student->id_card)
                    : null,
                'transcript_url' => $student->transcript
                    ? asset('storage/'.$student->transcript)
                    : null,
            ],
            'programmes' => $this->getProgrammes(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

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
        // Clean up stored files before deleting the student record
        $this->cleanupUploadedStudentFiles([
            'photo' => $student->photo,
            'id_card' => $student->id_card,
            'transcript' => $student->transcript,
        ]);

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:students,id'],
        ]);

        $ids = collect($data['ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()
                ->route('students.index')
                ->with('info', 'No students selected for deletion.');
        }

        $students = Student::query()
            ->whereIn('id', $ids->all())
            ->get(['id', 'photo', 'id_card', 'transcript']);

        foreach ($students as $student) {
            $this->cleanupUploadedStudentFiles([
                'photo' => $student->photo,
                'id_card' => $student->id_card,
                'transcript' => $student->transcript,
            ]);
        }

        Student::query()->whereIn('id', $ids->all())->delete();

        return redirect()
            ->route('students.index')
            ->with('success', "{$students->count()} student(s) deleted successfully.");
    }

    /**
     * Generate the next student number in the format STU0001, STU0002, ...
     */
    protected function generateStudentNo(bool $lockForUpdate = false): string
    {
        $query = Student::query()->orderByDesc('id');
        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $latest = $query->value('student_no');

        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'STU'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    protected function isDuplicateStudentNoException(QueryException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'students_student_no_unique')
            || str_contains($message, 'unique constraint failed: students.student_no')
            || (str_contains($message, 'duplicate') && str_contains($message, 'student_no'));
    }

    protected function isDuplicateUserIdException(QueryException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'students_user_id_unique')
            || str_contains($message, 'unique constraint failed: students.user_id')
            || (str_contains($message, 'duplicate') && str_contains($message, 'user_id'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function cleanupUploadedStudentFiles(array $data): void
    {
        if (! empty($data['photo']) && is_string($data['photo'])) {
            ImageService::delete($data['photo']);
        }

        if (! empty($data['id_card']) && is_string($data['id_card'])) {
            $this->deleteDocument($data['id_card']);
        }

        if (! empty($data['transcript']) && is_string($data['transcript'])) {
            $this->deleteDocument($data['transcript']);
        }
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
        $filename = $folder.'/'.uniqid().'.'.$extension;

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
        if (! $path) {
            return false;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
