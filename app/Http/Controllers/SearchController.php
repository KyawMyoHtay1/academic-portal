<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    private const LIMIT_PER_TYPE = 5;

    private const MIN_QUERY_LENGTH = 2;

    /**
     * Global search across all entities, scoped by user role.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < self::MIN_QUERY_LENGTH) {
            return response()->json(['results' => []]);
        }

        $term = '%'.$q.'%';

        if (Auth::guest()) {
            return response()->json(['results' => $this->searchGuest($term, $q)]);
        }

        $user = Auth::user();
        $results = [];
        if ($user->isStaff()) {
            $results = $this->searchStaff($term);
        } elseif ($user->isTeacher()) {
            $results = $this->searchTeacher($user, $term);
        } elseif ($user->isStudent()) {
            $results = $this->searchStudent($user, $term);
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Guest search: public courses, public announcements (news), and static guest pages.
     */
    private function searchGuest(string $term, string $rawQuery): array
    {
        $results = [];
        $q = strtolower($rawQuery);

        // Public courses
        $courses = Course::where(function ($query) use ($term) {
            $query->where('course_code', 'like', $term)->orWhere('title', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'course_code', 'title']);

        foreach ($courses as $c) {
            $results[] = [
                'type' => 'course',
                'id' => 'course-'.$c->id,
                'title' => $c->title,
                'subtitle' => $c->course_code,
                'url' => route('guest.courses').'#course-'.$c->id,
            ];
        }

        // Public announcements (News)
        $announcements = Announcement::query()
            ->currentlyVisible()
            ->visibleToUser(null)
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', $term)->orWhere('body', 'like', $term);
            })
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'title']);

        foreach ($announcements as $a) {
            $results[] = [
                'type' => 'announcement',
                'id' => 'announcement-'.$a->id,
                'title' => $a->title,
                'subtitle' => 'News',
                'url' => route('guest.news').'#announcement-'.$a->id,
            ];
        }

        // Static guest pages (match by title or keywords)
        $pages = [
            ['title' => 'Home', 'url' => route('guest.home'), 'keywords' => ['home', 'main', 'welcome']],
            ['title' => 'Courses', 'url' => route('guest.courses'), 'keywords' => ['courses', 'programs', 'curriculum']],
            ['title' => 'News', 'url' => route('guest.news'), 'keywords' => ['news', 'announcements', 'updates']],
            ['title' => 'About Us', 'url' => route('guest.about'), 'keywords' => ['about', 'us', 'university']],
            ['title' => 'Our Vision', 'url' => route('guest.vision'), 'keywords' => ['vision', 'mission', 'goals']],
            ['title' => 'Policies & Guidelines', 'url' => route('guest.policies'), 'keywords' => ['policies', 'guidelines', 'rules']],
            ['title' => 'Feedback', 'url' => route('guest.feedback'), 'keywords' => ['feedback', 'suggestions']],
            ['title' => 'Academic Services', 'url' => route('guest.services'), 'keywords' => ['academic', 'services']],
            ['title' => 'Support & Help Desk', 'url' => route('guest.support'), 'keywords' => ['support', 'help', 'desk', 'faq']],
            ['title' => 'Contact Us', 'url' => route('guest.contact'), 'keywords' => ['contact', 'us', 'email', 'phone']],
        ];

        foreach ($pages as $i => $page) {
            $titleLower = strtolower($page['title']);
            $matchTitle = str_contains($titleLower, $q);
            $matchKeyword = collect($page['keywords'])->contains(fn ($k) => str_contains($k, $q));
            if ($matchTitle || $matchKeyword) {
                $results[] = [
                    'type' => 'page',
                    'id' => 'page-'.$i,
                    'title' => $page['title'],
                    'subtitle' => 'Page',
                    'url' => $page['url'],
                ];
            }
        }

        return $results;
    }

    private function searchStaff(string $term): array
    {
        $results = [];

        // Students
        $students = Student::where(function ($query) use ($term) {
            $query->where('full_name', 'like', $term)
                ->orWhere('student_no', 'like', $term)
                ->orWhere('email', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'full_name', 'student_no', 'email']);

        foreach ($students as $s) {
            $results[] = [
                'type' => 'student',
                'id' => $s->id,
                'title' => $s->full_name,
                'subtitle' => $s->student_no ? "Student #{$s->student_no}" : $s->email,
                'url' => route('students.edit', $s),
            ];
        }

        // Users
        $users = User::where(function ($query) use ($term) {
            $query->where('name', 'like', $term)->orWhere('email', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'name', 'email', 'role']);

        foreach ($users as $u) {
            $results[] = [
                'type' => 'user',
                'id' => $u->id,
                'title' => $u->name,
                'subtitle' => $u->email.' ('.$u->role.')',
                'url' => route('admin.users.edit', $u),
            ];
        }

        // Courses
        $courses = Course::where(function ($query) use ($term) {
            $query->where('course_code', 'like', $term)->orWhere('title', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'course_code', 'title']);

        foreach ($courses as $c) {
            $results[] = [
                'type' => 'course',
                'id' => $c->id,
                'title' => $c->title,
                'subtitle' => $c->course_code,
                'url' => route('admin.courses.edit', $c),
            ];
        }

        // Subjects
        $subjects = Subject::where(function ($query) use ($term) {
            $query->where('subject_code', 'like', $term)->orWhere('title', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'subject_code', 'title']);

        foreach ($subjects as $s) {
            $results[] = [
                'type' => 'subject',
                'id' => $s->id,
                'title' => $s->title,
                'subtitle' => $s->subject_code,
                'url' => route('admin.subjects.edit', $s),
            ];
        }

        // Announcements
        $announcements = Announcement::where('title', 'like', $term)
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'title']);

        foreach ($announcements as $a) {
            $results[] = [
                'type' => 'announcement',
                'id' => $a->id,
                'title' => $a->title,
                'subtitle' => null,
                'url' => route('admin.announcements.edit', $a),
            ];
        }

        return $results;
    }

    private function searchTeacher($user, string $term): array
    {
        $results = [];

        // Courses and subjects the teacher teaches
        $courseIds = $user->teachingCourses()->pluck('courses.id');
        $subjectIds = $user->teachingSubjects()->pluck('subjects.id')->filter()->values();

        if ($courseIds->isEmpty() && $subjectIds->isEmpty()) {
            return $results;
        }

        $courses = Course::whereIn('id', $courseIds)
            ->where(function ($query) use ($term) {
                $query->where('course_code', 'like', $term)->orWhere('title', 'like', $term);
            })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'course_code', 'title']);

        foreach ($courses as $c) {
            $results[] = [
                'type' => 'course',
                'id' => $c->id,
                'title' => $c->title,
                'subtitle' => $c->course_code,
                'url' => route('teacher.courses.index'),
            ];
        }

        // Subjects the teacher teaches
        $subjects = Subject::whereIn('id', $subjectIds)
            ->where(function ($query) use ($term) {
                $query->where('subject_code', 'like', $term)->orWhere('title', 'like', $term);
            })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'subject_code', 'title', 'course_id']);

        foreach ($subjects as $s) {
            $results[] = [
                'type' => 'subject',
                'id' => $s->id,
                'title' => $s->title,
                'subtitle' => $s->subject_code,
                'url' => route('teacher.grades.show', $s),
            ];
        }

        // Assignments (in teacher's subjects)
        $assignments = Assignment::whereIn('subject_id', $subjectIds)
            ->where('title', 'like', $term)
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'title', 'subject_id']);

        foreach ($assignments as $a) {
            $results[] = [
                'type' => 'assignment',
                'id' => $a->id,
                'title' => $a->title,
                'subtitle' => 'Assignment',
                'url' => route('teacher.assignments.submissions', $a),
            ];
        }

        // Announcements (visible to teacher)
        $announcements = Announcement::query()
            ->currentlyVisible()
            ->visibleToUser($user)
            ->where('title', 'like', $term)
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'title']);

        foreach ($announcements as $a) {
            $results[] = [
                'type' => 'announcement',
                'id' => $a->id,
                'title' => $a->title,
                'subtitle' => null,
                'url' => route('announcements.index'),
            ];
        }

        return $results;
    }

    private function searchStudent($user, string $term): array
    {
        $results = [];
        $student = $user->student;
        if (! $student) {
            return $results;
        }

        $enrolledCourseIds = $student->courses()->wherePivot('status', 'approved')->pluck('courses.id');

        // Courses (enrolled and available)
        $courses = Course::where(function ($query) use ($term) {
            $query->where('course_code', 'like', $term)->orWhere('title', 'like', $term);
        })
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'course_code', 'title']);

        foreach ($courses as $c) {
            $url = in_array($c->id, $enrolledCourseIds->toArray())
                ? route('my-courses.index')
                : route('courses.index');
            $results[] = [
                'type' => 'course',
                'id' => $c->id,
                'title' => $c->title,
                'subtitle' => $c->course_code,
                'url' => $url,
            ];
        }

        // Assignments (for enrolled courses/subjects)
        $assignmentIds = Assignment::whereHas('subject', function ($q) use ($enrolledCourseIds) {
            $q->whereIn('course_id', $enrolledCourseIds);
        })
            ->where('title', 'like', $term)
            ->where('status', Assignment::STATUS_PUBLISHED)
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->pluck('id');

        $assignments = Assignment::whereIn('id', $assignmentIds)->get(['id', 'title']);
        foreach ($assignments as $a) {
            $results[] = [
                'type' => 'assignment',
                'id' => $a->id,
                'title' => $a->title,
                'subtitle' => 'Assignment',
                'url' => route('student.assignments.show', $a),
            ];
        }

        // Announcements
        $announcements = Announcement::query()
            ->currentlyVisible()
            ->visibleToUser($user)
            ->where('title', 'like', $term)
            ->orderByDesc('created_at')
            ->limit(self::LIMIT_PER_TYPE)
            ->get(['id', 'title']);

        foreach ($announcements as $a) {
            $results[] = [
                'type' => 'announcement',
                'id' => $a->id,
                'title' => $a->title,
                'subtitle' => null,
                'url' => route('announcements.index'),
            ];
        }

        return $results;
    }
}
