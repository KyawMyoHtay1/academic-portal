<?php

namespace Tests\Feature;

use App\Jobs\SendLowAttendanceAlertsJob;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use ReflectionProperty;
use Tests\TestCase;

class StaffAttendanceAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_run_bypasses_cooldown_by_default(): void
    {
        Bus::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [$course, $subject] = $this->createCourseWithSubject('CSC100', 'SUB100');
        $student = $this->createStudentWithAttendance($course, $subject, 'STU1001');

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.attendance.alerts.run'), [
                'course_id' => $course->id,
                'threshold' => 75,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Bus::assertDispatched(SendLowAttendanceAlertsJob::class, function (SendLowAttendanceAlertsJob $job) use ($student) {
            $cooldown = new ReflectionProperty($job, 'cooldownDaysOverride');
            $cooldown->setAccessible(true);

            $threshold = new ReflectionProperty($job, 'thresholdOverride');
            $threshold->setAccessible(true);

            $snapshots = new ReflectionProperty($job, 'studentSnapshots');
            $snapshots->setAccessible(true);

            $payload = $snapshots->getValue($job);

            return $threshold->getValue($job) === 75.0
                && $cooldown->getValue($job) === 0
                && count($payload) === 1
                && (int) $payload[0]['student_id'] === $student->id
                && (float) $payload[0]['rate'] === 0.0;
        });
    }

    public function test_manual_run_respects_explicit_cooldown_override(): void
    {
        Bus::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [$course, $subject] = $this->createCourseWithSubject('CSC200', 'SUB200');
        $student = $this->createStudentWithAttendance($course, $subject, 'STU2001');

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.attendance.alerts.run'), [
                'course_id' => $course->id,
                'threshold' => 68.5,
                'cooldown_days' => 4,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Bus::assertDispatched(SendLowAttendanceAlertsJob::class, function (SendLowAttendanceAlertsJob $job) use ($student) {
            $cooldown = new ReflectionProperty($job, 'cooldownDaysOverride');
            $cooldown->setAccessible(true);

            $threshold = new ReflectionProperty($job, 'thresholdOverride');
            $threshold->setAccessible(true);

            $snapshots = new ReflectionProperty($job, 'studentSnapshots');
            $snapshots->setAccessible(true);

            $payload = $snapshots->getValue($job);

            return $threshold->getValue($job) === 68.5
                && $cooldown->getValue($job) === 4
                && count($payload) === 1
                && (int) $payload[0]['student_id'] === $student->id
                && (float) $payload[0]['rate'] === 0.0;
        });
    }

    public function test_manual_run_only_dispatches_students_matching_current_filters(): void
    {
        Bus::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        [$courseOne, $subjectOne] = $this->createCourseWithSubject('CSC300', 'SUB300');
        [$courseTwo, $subjectTwo] = $this->createCourseWithSubject('CSC400', 'SUB400');

        $matchingStudent = $this->createStudentWithAttendance($courseOne, $subjectOne, 'STU3001');
        $this->createStudentWithAttendance($courseTwo, $subjectTwo, 'STU4001');

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.attendance.alerts.run'), [
                'course_id' => $courseOne->id,
                'threshold' => 75,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Bus::assertDispatched(SendLowAttendanceAlertsJob::class, function (SendLowAttendanceAlertsJob $job) use ($matchingStudent) {
            $snapshots = new ReflectionProperty($job, 'studentSnapshots');
            $snapshots->setAccessible(true);

            $payload = $snapshots->getValue($job);

            return count($payload) === 1
                && (int) $payload[0]['student_id'] === $matchingStudent->id
                && (float) $payload[0]['rate'] === 0.0;
        });
    }

    private function createCourseWithSubject(string $courseCode, string $subjectCode): array
    {
        $course = Course::create([
            'course_code' => $courseCode,
            'title' => $courseCode.' Course',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => $subjectCode,
            'title' => $subjectCode.' Subject',
            'credits' => 20,
        ]);

        return [$course, $subject];
    }

    private function createStudentWithAttendance(Course $course, Subject $subject, string $studentNo): Student
    {
        $user = User::factory()->create([
            'role' => 'student',
            'preferences' => [
                'notify_attendance' => true,
                'email_notifications' => true,
            ],
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_no' => $studentNo,
            'programme' => 'Computer Science',
            'intake_year' => '2025',
        ]);

        $student->courses()->attach($course->id, ['status' => 'approved']);

        Attendance::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'absent',
        ]);

        return $student;
    }
}
