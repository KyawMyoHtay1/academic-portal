<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnrollmentReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_approve_pending_enrollment_request(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [, $student] = $this->createStudentUser();
        $course = $this->createCourse();

        $enrollmentId = DB::table('course_student')->insertGetId([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.enrollments.approve', $enrollmentId));

        $response
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_student', [
            'id' => $enrollmentId,
            'status' => 'approved',
        ]);
    }

    public function test_non_staff_cannot_approve_pending_enrollment_request(): void
    {
        [$studentUser, $student] = $this->createStudentUser();
        $course = $this->createCourse();

        $enrollmentId = DB::table('course_student')->insertGetId([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($studentUser)
            ->post(route('admin.enrollments.approve', $enrollmentId));

        $response->assertForbidden();

        $this->assertDatabaseHas('course_student', [
            'id' => $enrollmentId,
            'status' => 'pending',
        ]);
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Student}
     */
    private function createStudentUser(): array
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_no' => 'STU'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $user->name,
            'email' => $user->email,
            'programme' => 'BSc Computing',
            'intake_year' => '2026',
        ]);

        return [$user, $student];
    }

    private function createCourse(): Course
    {
        return Course::create([
            'course_code' => 'CSE330',
            'title' => 'Cloud Platforms',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);
    }
}
