<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_course_index_includes_enrollment_counts_for_filtering(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [, $student] = $this->createStudentUser();

        $courseWithEnrollment = Course::create([
            'course_code' => 'CSE401',
            'title' => 'Cloud Computing',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $courseWithoutEnrollment = Course::create([
            'course_code' => 'CSE402',
            'title' => 'Edge Systems',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $courseWithEnrollment->students()->attach($student->id, [
            'status' => 'approved',
        ]);

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.courses.index'));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame('Admin/Courses/Index', $page['component']);

        $courses = collect($page['props']['courses'] ?? []);

        $withEnrollment = $courses->firstWhere('id', $courseWithEnrollment->id);
        $withoutEnrollment = $courses->firstWhere('id', $courseWithoutEnrollment->id);

        $this->assertNotNull($withEnrollment);
        $this->assertNotNull($withoutEnrollment);
        $this->assertSame(1, (int) $withEnrollment['enrolled_students_count']);
        $this->assertSame(0, (int) $withoutEnrollment['enrolled_students_count']);
    }

    public function test_staff_can_delete_course_when_only_pending_or_rejected_requests_exist(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [, $studentA] = $this->createStudentUser();
        [, $studentB] = $this->createStudentUser();

        $course = Course::create([
            'course_code' => 'CSE403',
            'title' => 'Digital Platforms',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        DB::table('course_student')->insert([
            [
                'course_id' => $course->id,
                'student_id' => $studentA->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $course->id,
                'student_id' => $studentB->id,
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($staff)
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_staff_cannot_delete_course_when_withdrawal_pending_exists(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [, $student] = $this->createStudentUser();

        $course = Course::create([
            'course_code' => 'CSE404',
            'title' => 'Enterprise Systems',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        DB::table('course_student')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'status' => 'withdrawal_pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staff)
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
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
}
