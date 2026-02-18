<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherCoursesViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_course_index_includes_direct_course_and_subject_based_assignments(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $otherTeacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $courseDirect = Course::create([
            'course_code' => 'TCH100',
            'title' => 'Teaching Methods',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $courseFromSubject = Course::create([
            'course_code' => 'TCH200',
            'title' => 'Curriculum Design',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $subject = Subject::create([
            'course_id' => $courseFromSubject->id,
            'subject_code' => 'CUR201',
            'title' => 'Curriculum Workshop',
            'credits' => 20,
        ]);

        $otherCourse = Course::create([
            'course_code' => 'TCH999',
            'title' => 'Hidden Course',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $teacher->teachingCourses()->attach($courseDirect->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subject->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherCourse->teachers()->attach($otherTeacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.courses.index'));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame('Teacher/MyCourses', $page['component']);

        $courses = collect($page['props']['courses'] ?? []);
        $courseIds = $courses->pluck('id')->all();

        $this->assertContains($courseDirect->id, $courseIds);
        $this->assertContains($courseFromSubject->id, $courseIds);
        $this->assertNotContains($otherCourse->id, $courseIds);

        $directCoursePayload = $courses->firstWhere('id', $courseDirect->id);
        $this->assertNotNull($directCoursePayload);
        $this->assertCount(0, $directCoursePayload['subjects']);

        $subjectCoursePayload = $courses->firstWhere('id', $courseFromSubject->id);
        $this->assertNotNull($subjectCoursePayload);
        $this->assertSame('CUR201', $subjectCoursePayload['subjects'][0]['subject_code']);
    }

    public function test_non_teacher_cannot_access_teacher_course_index(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($staff)
            ->get(route('teacher.courses.index'))
            ->assertForbidden();
    }
}

