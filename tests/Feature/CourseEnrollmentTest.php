<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_course_enrollment_request(): void
    {
        [$user, $student] = $this->createStudentUser();

        $course = Course::create([
            'course_code' => 'CSE101',
            'title' => 'Introduction to Computing',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('courses.enroll', $course));

        $response
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_student', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_pending_enrollment_request_is_rejected(): void
    {
        [$user, $student] = $this->createStudentUser();

        $course = Course::create([
            'course_code' => 'CSE102',
            'title' => 'Data Structures',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        DB::table('course_student')->insert([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('courses.enroll', $course));

        $response
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('error');

        $count = DB::table('course_student')
            ->where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->count();

        $this->assertSame(1, $count);
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
