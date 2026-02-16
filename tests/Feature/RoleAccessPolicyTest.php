<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_payment_for_own_fee(): void
    {
        [$user, $student] = $this->createStudentUser();

        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => 1200,
            'description' => 'Semester fee',
            'status' => 'pending',
            'due_date' => now()->toDateString(),
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('student.fees.submit-payment', $fee));

        $response->assertRedirect(route('student.fees.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'status' => 'payment_pending',
        ]);
    }

    public function test_student_cannot_submit_payment_for_other_students_fee(): void
    {
        [$attacker] = $this->createStudentUser();
        [, $ownerStudent] = $this->createStudentUser();

        $fee = Fee::create([
            'student_id' => $ownerStudent->id,
            'amount' => 800,
            'description' => 'Lab fee',
            'status' => 'pending',
            'due_date' => now()->toDateString(),
        ]);

        $response = $this
            ->actingAs($attacker)
            ->post(route('student.fees.submit-payment', $fee));

        $response->assertForbidden();

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'status' => 'pending',
        ]);
    }

    public function test_teacher_cannot_submit_student_course_enrollment_request(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::create([
            'course_code' => 'CSE300',
            'title' => 'Networks',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $response = $this
            ->actingAs($teacher)
            ->post(route('courses.enroll', $course));

        $response->assertForbidden();
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
