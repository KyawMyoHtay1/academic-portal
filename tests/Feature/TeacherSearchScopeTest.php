<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherSearchScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_search_returns_only_teacher_scoped_entities(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $otherTeacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $courseViaSubject = Course::create([
            'course_code' => 'ALP101',
            'title' => 'Alpha Course via Subject',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $courseDirect = Course::create([
            'course_code' => 'ALP201',
            'title' => 'Alpha Course Direct',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $hiddenCourse = Course::create([
            'course_code' => 'ALP999',
            'title' => 'Alpha Hidden Course',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $subject = Subject::create([
            'course_id' => $courseViaSubject->id,
            'subject_code' => 'ALP-SUB-1',
            'title' => 'Alpha Subject',
            'credits' => 20,
        ]);

        $hiddenSubject = Subject::create([
            'course_id' => $hiddenCourse->id,
            'subject_code' => 'ALP-SUB-9',
            'title' => 'Alpha Hidden Subject',
            'credits' => 20,
        ]);

        $subject->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $hiddenSubject->teachers()->attach($otherTeacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $teacher->teachingCourses()->attach($courseDirect->id, [
            'created_at' => now(),
            'updated_at' => now(),
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

        Assignment::create([
            'subject_id' => $hiddenSubject->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Alpha Hidden Assignment',
            'description' => 'Hidden assignment',
            'due_date' => now()->addWeek()->toDateString(),
            'max_score' => 100,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        Announcement::create([
            'title' => 'Alpha Teacher Notice',
            'body' => 'Visible to teachers',
            'user_id' => $staff->id,
            'audience' => ['roles' => ['teacher']],
        ]);

        Announcement::create([
            'title' => 'Alpha Student Notice',
            'body' => 'Not visible to teachers',
            'user_id' => $staff->id,
            'audience' => ['roles' => ['student']],
        ]);

        $response = $this
            ->actingAs($teacher)
            ->getJson(route('search', ['q' => 'Alpha']));

        $response->assertOk();

        $results = collect($response->json('results'));
        $titles = $results->pluck('title')->all();
        $types = $results->pluck('type')->all();

        $this->assertContains('Alpha Course via Subject', $titles);
        $this->assertContains('Alpha Course Direct', $titles);
        $this->assertContains('Alpha Subject', $titles);
        $this->assertContains('Alpha Assignment', $titles);
        $this->assertContains('Alpha Teacher Notice', $titles);

        $this->assertNotContains('Alpha Hidden Course', $titles);
        $this->assertNotContains('Alpha Hidden Subject', $titles);
        $this->assertNotContains('Alpha Hidden Assignment', $titles);
        $this->assertNotContains('Alpha Student Notice', $titles);

        $this->assertContains('course', $types);
        $this->assertContains('subject', $types);
        $this->assertContains('assignment', $types);
        $this->assertContains('announcement', $types);
        $this->assertNotContains('student', $types);
        $this->assertNotContains('user', $types);

        $results->each(function (array $item): void {
            $this->assertArrayHasKey('type', $item);
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('subtitle', $item);
            $this->assertArrayHasKey('url', $item);
        });
    }

    public function test_teacher_search_includes_visible_announcements_without_teaching_assignments(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        Announcement::create([
            'title' => 'Policy Update',
            'body' => 'Teacher policy update',
            'user_id' => $staff->id,
            'audience' => ['roles' => ['teacher']],
        ]);

        $response = $this
            ->actingAs($teacher)
            ->getJson(route('search', ['q' => 'Policy']));

        $response->assertOk();

        $results = collect($response->json('results'));
        $announcement = $results->firstWhere('title', 'Policy Update');

        $this->assertNotNull($announcement);
        $this->assertSame('announcement', $announcement['type']);
    }

    public function test_search_enforces_minimum_query_length_for_teacher(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $this->actingAs($teacher)
            ->getJson(route('search', ['q' => 'a']))
            ->assertOk()
            ->assertJsonCount(0, 'results');
    }
}
