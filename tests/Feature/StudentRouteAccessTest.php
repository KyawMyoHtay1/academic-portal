<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_cannot_access_student_course_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $this->actingAs($teacher)
            ->get(route('courses.index'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->get(route('my-courses.index'))
            ->assertForbidden();
    }

    public function test_staff_cannot_access_student_profile_route(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($staff)
            ->get(route('student.profile.show'))
            ->assertForbidden();
    }
}
