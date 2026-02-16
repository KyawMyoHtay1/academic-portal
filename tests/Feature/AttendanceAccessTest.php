<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_own_attendance_report(): void
    {
        [$user, $student] = $this->createStudentUser();

        $course = Course::create([
            'course_code' => 'CSE310',
            'title' => 'Distributed Systems',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SUB310',
            'title' => 'Distributed Systems Core',
            'credits' => 20,
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Attendance::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('student.attendance.index'));

        $response->assertOk();
        $response->assertSee('SUB310');
        $response->assertSee('CSE310');
    }

    public function test_teacher_can_access_teacher_attendance_module(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.attendance.index'));

        $response->assertOk();
    }

    public function test_student_cannot_access_teacher_attendance_module(): void
    {
        [$studentUser] = $this->createStudentUser();

        $response = $this
            ->actingAs($studentUser)
            ->get(route('teacher.attendance.index'));

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
