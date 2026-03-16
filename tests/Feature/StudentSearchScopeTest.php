<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentSearchScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_search_without_student_profile_still_returns_courses_and_announcements(): void
    {
        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        Course::create([
            'course_code' => 'ALP101',
            'title' => 'Alpha Course',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        Announcement::create([
            'title' => 'Alpha Notice',
            'body' => 'Visible to students',
            'user_id' => $staff->id,
            'audience' => ['roles' => ['student']],
        ]);

        $response = $this
            ->actingAs($studentUser)
            ->getJson(route('search', ['q' => 'Alpha']));

        $response->assertOk();

        $results = collect($response->json('results'));
        $titles = $results->pluck('title')->all();
        $types = $results->pluck('type')->all();

        $this->assertContains('Alpha Course', $titles);
        $this->assertContains('Alpha Notice', $titles);
        $this->assertContains('course', $types);
        $this->assertContains('announcement', $types);
    }

    public function test_student_search_includes_assignments_for_withdrawal_pending_courses(): void
    {
        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_no' => 'STU'.str_pad((string) $studentUser->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $studentUser->name,
            'email' => $studentUser->email,
            'programme' => 'BSc Computing',
            'intake_year' => '2026',
        ]);

        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::create([
            'course_code' => 'ALP201',
            'title' => 'Alpha Course',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'ALP-SUB-1',
            'title' => 'Alpha Subject',
            'credits' => 20,
        ]);

        Assignment::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Alpha Assignment',
            'description' => 'Visible assignment',
            'due_date' => now()->addWeek()->toDateString(),
            'max_score' => 100,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        DB::table('course_student')->insert([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'withdrawal_pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($studentUser)
            ->getJson(route('search', ['q' => 'Alpha']));

        $response->assertOk();

        $results = collect($response->json('results'));
        $titles = $results->pluck('title')->all();
        $types = $results->pluck('type')->all();

        $this->assertContains('Alpha Assignment', $titles);
        $this->assertContains('assignment', $types);
    }
}
