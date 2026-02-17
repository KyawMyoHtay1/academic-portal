<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Services\SubjectGradeCalculator;
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

        if (! $student) {
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
            ->map(function ($course) use ($student) {
                $calculator = new SubjectGradeCalculator();
                
                $subjectsWithGrades = $course->subjects->map(function ($subject) use ($student, $calculator) {
                    $grade = $subject->grades->first();
                    $assignmentData = $calculator->calculateSuggestedGrade($subject->id, $student->id);

                    return [
                        'id' => $subject->id,
                        'subject_code' => $subject->subject_code,
                        'title' => $subject->title,
                        'photo' => $subject->photo,
                        'score' => $grade?->score,
                        'grade_status' => $grade?->status,
                        // Assignment-based computed grade
                        'computed_grade' => $assignmentData['computed_grade'],
                        'assignment_breakdown' => $assignmentData['breakdown'],
                        'total_assignments' => $assignmentData['total_assignments'],
                        'graded_assignments' => $assignmentData['graded_assignments'],
                        'ungraded_assignments' => $assignmentData['ungraded_assignments'],
                        'has_assignments' => $assignmentData['has_assignments'],
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
