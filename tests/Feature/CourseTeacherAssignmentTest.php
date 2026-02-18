<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTeacherAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_assign_non_teacher_user_to_course(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $nonTeacher = User::factory()->create([
            'role' => 'student',
        ]);

        $course = Course::create([
            'course_code' => 'CSC430',
            'title' => 'Cloud Security',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $this->actingAs($staff)
            ->from(route('admin.courses.assign-teachers', $course))
            ->put(route('admin.courses.assign-teachers.update', $course), [
                'teacher_ids' => [$nonTeacher->id],
            ])
            ->assertRedirect(route('admin.courses.assign-teachers', $course))
            ->assertSessionHasErrors('teacher_ids.0');

        $this->assertDatabaseMissing('course_teacher', [
            'course_id' => $course->id,
            'user_id' => $nonTeacher->id,
        ]);
    }
}

