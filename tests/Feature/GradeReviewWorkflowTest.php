<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_approve_pending_grade(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $grade = $this->createPendingGrade();

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.grades.approve', $grade), [
                'redirect_subject_id' => $grade->subject_id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'status' => Grade::STATUS_APPROVED,
            'reviewed_by' => $staff->id,
        ]);

        $this->assertDatabaseHas('grade_review_logs', [
            'grade_id' => $grade->id,
            'performed_by' => $staff->id,
            'action' => 'approved',
        ]);
    }

    public function test_non_staff_cannot_approve_pending_grade(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $grade = $this->createPendingGrade();

        $response = $this
            ->actingAs($teacher)
            ->post(route('admin.grades.approve', $grade));

        $response->assertForbidden();

        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'status' => Grade::STATUS_PENDING,
            'reviewed_by' => null,
        ]);
    }

    private function createPendingGrade(): Grade
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
            'course_code' => 'CSE200',
            'title' => 'Algorithms',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SUB200',
            'title' => 'Algorithms and Complexity',
            'credits' => 20,
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Grade::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'student_id' => $student->id,
            'graded_by' => $teacher->id,
            'score' => 78.5,
            'status' => Grade::STATUS_PENDING,
        ]);
    }
}
