<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StaffAttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_attendance_report_returns_low_attendance_students_without_server_error(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_no' => 'STU2001',
            'full_name' => 'Attendance Report Student',
            'email' => 'attendance-report-student@example.test',
            'programme' => 'Computer Science',
            'intake_year' => '2025',
        ]);

        $course = Course::create([
            'course_code' => 'CSC500',
            'title' => 'Applied Software Engineering',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'ASE500',
            'title' => 'Advanced Software Engineering',
            'credits' => 20,
        ]);

        $student->courses()->attach($course->id, ['status' => 'approved']);

        Attendance::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->subDay()->toDateString(),
            'status' => 'absent',
        ]);

        Attendance::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.attendance.report', [
                'threshold' => 75,
            ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Attendance/Report')
            ->has('lowAttendanceStudents', 1)
            ->where('lowAttendanceStudents.0.student_no', 'STU2001')
            ->where('lowAttendanceStudents.0.total', 2)
            ->where('lowAttendanceStudents.0.present', 1)
            ->where('lowAttendanceStudents.0.absent', 1)
            ->where('lowAttendanceStudents.0.rate', 50)
            ->where('lowAttendanceStudents.0.reason', '25.00% below global threshold')
        );
    }

    public function test_staff_attendance_report_serializes_grouped_rows_as_arrays(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_no' => 'STU2002',
            'full_name' => 'Array Serialization Student',
            'email' => 'array-serialization-student@example.test',
            'programme' => 'Computer Science',
            'intake_year' => '2025',
        ]);

        $emptyCourse = Course::create([
            'course_code' => 'CSC100',
            'title' => 'Empty Course',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $activeCourse = Course::create([
            'course_code' => 'CSC200',
            'title' => 'Active Course',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        Subject::create([
            'course_id' => $emptyCourse->id,
            'subject_code' => 'SUB100',
            'title' => 'Empty Subject',
            'credits' => 20,
        ]);

        $activeSubject = Subject::create([
            'course_id' => $activeCourse->id,
            'subject_code' => 'SUB200',
            'title' => 'Active Subject',
            'credits' => 20,
        ]);

        $student->courses()->attach($activeCourse->id, ['status' => 'approved']);

        Attendance::create([
            'subject_id' => $activeSubject->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.attendance.report'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Attendance/Report')
            ->has('byCourse', 1)
            ->where('byCourse.0.course_code', 'CSC200')
            ->has('bySubject', 1)
            ->where('bySubject.0.subject_code', 'SUB200')
        );
    }
}
