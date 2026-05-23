<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GuestPageDataService
{
    /**
     * @return array<string, mixed>
     */
    public function homeData(): array
    {
        return Cache::remember('guest:home', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();
            $totalAttendance = (int) Attendance::count();
            $presentAttendance = (int) Attendance::where('status', 'present')->count();

            return [
                'publicCourses' => Course::query()
                    ->orderBy('course_code')
                    ->take(6)
                    ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']),
                'publicAnnouncements' => Announcement::query()
                    ->currentlyVisible()
                    ->visibleToUser(null)
                    ->with(['author:id,name'])
                    ->orderByDesc('pinned')
                    ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(['id', 'user_id', 'title', 'body', 'created_at']),
                'stats' => [
                    'totalCourses' => $baseCounts['totalCourses'],
                    'totalStudents' => $baseCounts['totalStudents'],
                    'totalFaculty' => $baseCounts['totalFaculty'],
                    'successRate' => $totalAttendance > 0
                        ? round(($presentAttendance / $totalAttendance) * 100, 0)
                        : 95,
                    'totalCredits' => (int) Course::sum('credits'),
                    'uniqueSemesters' => (int) Course::distinct('semester')->count('semester'),
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function coursesData(): array
    {
        return Cache::remember('guest:courses', now()->addMinutes(5), function () {
            $courses = Course::query()
                ->orderBy('course_code')
                ->get([
                    'id',
                    'course_code',
                    'title',
                    'credits',
                    'semester',
                    'photo',
                ]);

            $totalCourses = $courses->count();
            $coursesWithEnrollments = (int) DB::table('course_student')
                ->where('status', 'approved')
                ->distinct('course_id')
                ->count('course_id');

            return [
                'courses' => $courses,
                'stats' => [
                    'totalCourses' => $totalCourses,
                    'uniqueSemesters' => $courses->unique('semester')->count(),
                    'totalCredits' => (int) $courses->sum('credits'),
                    'averageCredits' => $totalCourses > 0
                        ? round($courses->sum('credits') / $totalCourses, 1)
                        : 0,
                    'totalEnrollments' => (int) DB::table('course_student')
                        ->where('status', 'approved')
                        ->count(),
                    'availabilityRate' => $totalCourses > 0
                        ? round(($coursesWithEnrollments / $totalCourses) * 100, 0)
                        : 100,
                    'coursesWithEnrollments' => $coursesWithEnrollments,
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function courseShowData(int $courseId): array
    {
        return Cache::remember("guest:courses:show:{$courseId}", now()->addMinutes(5), function () use ($courseId) {
            $course = Course::query()
                ->withCount(['subjects', 'teachers'])
                ->findOrFail($courseId, [
                    'id',
                    'course_code',
                    'title',
                    'credits',
                    'semester',
                    'photo',
                ]);

            $approvedEnrollments = (int) DB::table('course_student')
                ->where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            $relatedCourses = Course::query()
                ->whereKeyNot($course->id)
                ->where('semester', $course->semester)
                ->orderBy('course_code')
                ->take(4)
                ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']);

            if ($relatedCourses->count() < 4) {
                $needed = 4 - $relatedCourses->count();
                $extra = Course::query()
                    ->whereKeyNot($course->id)
                    ->whereNotIn('id', $relatedCourses->pluck('id'))
                    ->orderBy('course_code')
                    ->take($needed)
                    ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']);
                $relatedCourses = $relatedCourses->concat($extra);
            }

            return [
                'course' => $course,
                'approvedEnrollments' => $approvedEnrollments,
                'relatedCourses' => $relatedCourses,
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function newsData(): array
    {
        return Cache::remember('guest:news', now()->addMinutes(5), function () {
            return [
                'announcements' => Announcement::query()
                    ->currentlyVisible()
                    ->visibleToUser(null)
                    ->orderByDesc('pinned')
                    ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
                    ->orderBy('created_at', 'desc')
                    ->get([
                        'id',
                        'title',
                        'body',
                        'priority',
                        'pinned',
                        'created_at',
                    ]),
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function newsShowData(int $announcementId): array
    {
        return Cache::remember("guest:news:show:{$announcementId}", now()->addMinutes(5), function () use ($announcementId) {
            $selected = Announcement::query()
                ->currentlyVisible()
                ->visibleToUser(null)
                ->whereKey($announcementId)
                ->firstOrFail([
                    'id',
                    'title',
                    'body',
                    'priority',
                    'pinned',
                    'created_at',
                ]);

            return [
                'announcement' => $selected,
                'relatedAnnouncements' => Announcement::query()
                    ->currentlyVisible()
                    ->visibleToUser(null)
                    ->whereKeyNot($selected->id)
                    ->orderByDesc('pinned')
                    ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get([
                        'id',
                        'title',
                        'body',
                        'priority',
                        'pinned',
                        'created_at',
                    ]),
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function aboutData(): array
    {
        return Cache::remember('guest:about', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();
            $oldestEnrollment = Student::query()
                ->whereNotNull('enrollment_date')
                ->orderBy('enrollment_date')
                ->value('enrollment_date');

            $yearsOfExcellence = 50;
            if ($oldestEnrollment) {
                $yearsOfExcellence = max(50, now()->year - Carbon::parse($oldestEnrollment)->year);
            }

            $uniqueProgrammes = (int) Student::query()
                ->distinct('programme')
                ->whereNotNull('programme')
                ->count('programme');

            return [
                'stats' => [
                    'yearsOfExcellence' => $yearsOfExcellence,
                    'totalStudents' => $baseCounts['totalStudents'],
                    'totalFaculty' => $baseCounts['totalFaculty'],
                    'totalOfferings' => $uniqueProgrammes > 0
                        ? $uniqueProgrammes
                        : $baseCounts['totalCourses'],
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function visionData(): array
    {
        return Cache::remember('guest:vision', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();

            return [
                'stats' => [
                    'totalUsers' => $baseCounts['totalUsers'],
                    'totalCourses' => $baseCounts['totalCourses'],
                    'totalEnrollments' => (int) DB::table('course_student')
                        ->where('status', 'approved')
                        ->count(),
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function servicesData(): array
    {
        return Cache::remember('guest:services', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();

            return [
                'stats' => [
                    'totalStudents' => $baseCounts['totalStudents'],
                    'totalCourses' => $baseCounts['totalCourses'],
                    'approvedEnrollments' => (int) DB::table('course_student')
                        ->where('status', 'approved')
                        ->count(),
                    'totalGrades' => (int) DB::table('grades')->count(),
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function supportData(): array
    {
        return Cache::remember('guest:support', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();

            return [
                'stats' => [
                    'totalStudents' => $baseCounts['totalStudents'],
                    'totalUsers' => $baseCounts['totalUsers'],
                    'supportTeamCount' => (int) User::query()
                        ->where('role', 'staff')
                        ->count(),
                ],
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function contactData(): array
    {
        return Cache::remember('guest:contact', now()->addMinutes(5), function () {
            $baseCounts = $this->baseCounts();

            return [
                'stats' => [
                    'totalUsers' => $baseCounts['totalUsers'],
                    'totalStudents' => $baseCounts['totalStudents'],
                    'totalFaculty' => $baseCounts['totalFaculty'],
                    'totalCourses' => $baseCounts['totalCourses'],
                ],
            ];
        });
    }

    /**
     * @return array{totalCourses:int,totalStudents:int,totalFaculty:int,totalUsers:int}
     */
    private function baseCounts(): array
    {
        return Cache::remember('guest:base-counts', now()->addMinutes(5), function () {
            return [
                'totalCourses' => (int) Course::count(),
                'totalStudents' => (int) Student::count(),
                'totalFaculty' => (int) User::query()
                    ->where('role', 'teacher')
                    ->count(),
                'totalUsers' => (int) User::count(),
            ];
        });
    }
}
