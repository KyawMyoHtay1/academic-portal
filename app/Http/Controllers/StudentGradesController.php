<?php

namespace App\Http\Controllers;

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

        $courses = $student->courses()
            ->with(['grades' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('course_code')
            ->get([
                'courses.id',
                'courses.course_code',
                'courses.title',
                'courses.credits',
                'courses.semester',
            ])
            ->map(function ($course) {
                $grade = $course->grades->first();
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'credits' => $course->credits,
                    'semester' => $course->semester,
                    'score' => $grade?->score,
                ];
            });

        return Inertia::render('Student/Grades/Index', [
            'courses' => $courses,
        ]);
    }
}
