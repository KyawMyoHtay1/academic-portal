<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentGradesController extends Controller
{
    /**
     * Display the authenticated student's grades (read-only).
     */
    public function index(): Response
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return Inertia::render('Student/Grades/Index', [
                'courses' => [],
                'message' => 'No student record found. Please contact administration to create your student profile.',
            ]);
        }

        // Get courses with subjects and their grades
        $courses = $student->courses()
            ->with(['subjects' => function ($query) {
                $query->select('subjects.id', 'subjects.course_id', 'subjects.subject_code', 'subjects.title', 'subjects.photo');
            }, 'subjects.grades' => function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', Grade::STATUS_APPROVED);
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.credits',
                'courses.semester',
                'courses.photo',
            ])
            ->map(function ($course) {
                $subjectsWithGrades = $course->subjects->map(function ($subject) {
                    $grade = $subject->grades->first();
                    return [
                        'id' => $subject->id,
                        'subject_code' => $subject->subject_code,
                        'title' => $subject->title,
                        'photo' => $subject->photo,
                        'score' => $grade?->score,
                    ];
                });
                
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'credits' => $course->credits,
                    'semester' => $course->semester,
                    'photo' => $course->photo,
                    'subjects' => $subjectsWithGrades,
                ];
            });

        // Calculate overall GPA
        $gpa = $student->calculateGPA();

        return Inertia::render('Student/Grades/Index', [
            'courses' => $courses,
            'gpa' => $gpa,
            'totalGrades' => $student->grades()
                ->where('status', Grade::STATUS_APPROVED)
                ->whereNotNull('score')
                ->count(),
        ]);
    }
}
