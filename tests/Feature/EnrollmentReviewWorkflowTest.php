<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
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

    public function test_staff_cannot_approve_pending_enrollment_with_schedule_conflict(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [, $student] = $this->createStudentUser();

        $approvedCourse = Course::create([
            'course_code' => 'CSE401',
            'title' => 'Distributed Systems',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $pendingCourse = Course::create([
            'course_code' => 'CSE402',
            'title' => 'Advanced Networks',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $approvedSubject = Subject::create([
            'course_id' => $approvedCourse->id,
            'subject_code' => 'DS401',
            'title' => 'Distributed Systems Subject',
            'credits' => 20,
        ]);

        $pendingSubject = Subject::create([
            'course_id' => $pendingCourse->id,
            'subject_code' => 'AN402',
            'title' => 'Advanced Networks Subject',
            'credits' => 20,
        ]);

        Timetable::create([
            'subject_id' => $approvedSubject->id,
            'day_of_week' => 'Tuesday',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'location' => 'Lab 1',
        ]);

        Timetable::create([
            'subject_id' => $pendingSubject->id,
            'day_of_week' => 'Tuesday',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Lab 2',
        ]);

        DB::table('course_student')->insert([
            [
                'student_id' => $student->id,
                'course_id' => $approvedCourse->id,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $pendingEnrollmentId = DB::table('course_student')->insertGetId([
            'student_id' => $student->id,
            'course_id' => $pendingCourse->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.enrollments.approve', $pendingEnrollmentId));

        $response
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('course_student', [
            'id' => $pendingEnrollmentId,
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
