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

    public function test_reapply_after_rejection_is_blocked_when_schedule_conflicts_with_approved_course(): void
    {
        [$user, $student] = $this->createStudentUser();

        $approvedCourse = Course::create([
            'course_code' => 'CSE201',
            'title' => 'Algorithms',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $rejectedCourse = Course::create([
            'course_code' => 'CSE202',
            'title' => 'Operating Systems',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $approvedSubject = Subject::create([
            'course_id' => $approvedCourse->id,
            'subject_code' => 'ALG201',
            'title' => 'Algorithms Subject',
            'credits' => 20,
        ]);

        $rejectedSubject = Subject::create([
            'course_id' => $rejectedCourse->id,
            'subject_code' => 'OS202',
            'title' => 'Operating Systems Subject',
            'credits' => 20,
        ]);

        Timetable::create([
            'subject_id' => $approvedSubject->id,
            'day_of_week' => 'Monday',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Room A',
        ]);

        Timetable::create([
            'subject_id' => $rejectedSubject->id,
            'day_of_week' => 'Monday',
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'location' => 'Room B',
        ]);

        DB::table('course_student')->insert([
            [
                'student_id' => $student->id,
                'course_id' => $approvedCourse->id,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $student->id,
                'course_id' => $rejectedCourse->id,
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('courses.enroll', $rejectedCourse));

        $response
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('course_student', [
            'student_id' => $student->id,
            'course_id' => $rejectedCourse->id,
            'status' => 'rejected',
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
