<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
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

    public function test_teacher_cannot_mark_attendance_for_pending_enrollment_student(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

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

        $course = Course::create([
            'course_code' => 'CSE320',
            'title' => 'Cloud Systems',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SUB320',
            'title' => 'Cloud Systems Core',
            'credits' => 20,
        ]);

        DB::table('subject_teacher')->insert([
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this
            ->actingAs($teacher)
            ->from(route('teacher.attendance.show', $subject))
            ->post(route('teacher.attendance.store', $subject), [
                'date' => now()->toDateString(),
                'attendance' => [
                    [
                        'student_id' => $student->id,
                        'status' => 'present',
                    ],
                ],
            ])
            ->assertRedirect(route('teacher.attendance.show', $subject))
            ->assertSessionHasErrors('attendance');

        $this->assertDatabaseMissing('attendances', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_non_assigned_teacher_cannot_access_or_record_attendance_for_subject(): void
    {
        $assignedTeacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $nonAssignedTeacher = User::factory()->create([
            'role' => 'teacher',
        ]);

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

        $course = Course::create([
            'course_code' => 'CSE321',
            'title' => 'Secure Systems',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SUB321',
            'title' => 'Secure Systems Core',
            'credits' => 20,
        ]);

        DB::table('subject_teacher')->insert([
            'subject_id' => $subject->id,
            'user_id' => $assignedTeacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this
            ->actingAs($nonAssignedTeacher)
            ->get(route('teacher.attendance.show', $subject))
            ->assertForbidden();

        $this
            ->actingAs($nonAssignedTeacher)
            ->post(route('teacher.attendance.store', $subject), [
                'date' => now()->toDateString(),
                'attendance' => [
                    [
                        'student_id' => $student->id,
                        'status' => 'present',
                    ],
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('attendances', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
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
