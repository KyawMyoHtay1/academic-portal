<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\GradePublished;
use App\Notifications\GradeReviewOutcome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function test_staff_reject_requires_reason(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $grade = $this->createPendingGrade();

        $this->actingAs($staff)
            ->post(route('admin.grades.reject', $grade), [])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'status' => Grade::STATUS_PENDING,
            'reviewed_by' => null,
        ]);

        $this->assertDatabaseMissing('grade_review_logs', [
            'grade_id' => $grade->id,
            'action' => 'rejected',
        ]);
    }

    public function test_staff_approval_notifies_teacher_and_student(): void
    {
        Notification::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $grade = $this->createPendingGrade()->refresh();
        $teacher = $grade->grader;
        $studentUser = $grade->student?->user;

        $this->actingAs($staff)
            ->post(route('admin.grades.approve', $grade), [
                'redirect_subject_id' => $grade->subject_id,
            ])
            ->assertSessionHas('success');

        Notification::assertSentTo($teacher, GradeReviewOutcome::class, function ($notification) use ($teacher, $grade) {
            $data = $notification->toArray($teacher);

            return ($data['decision'] ?? null) === 'approved'
                && ($data['grade_id'] ?? null) === $grade->id;
        });

        Notification::assertSentTo($studentUser, GradePublished::class);
    }

    public function test_staff_rejection_notifies_teacher_with_reason(): void
    {
        Notification::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $grade = $this->createPendingGrade()->refresh();
        $teacher = $grade->grader;
        $studentUser = $grade->student?->user;

        $this->actingAs($staff)
            ->post(route('admin.grades.reject', $grade), [
                'reason' => 'Calculation mismatch',
            ])
            ->assertSessionHas('success');

        Notification::assertSentTo($teacher, GradeReviewOutcome::class, function ($notification) use ($teacher, $grade) {
            $data = $notification->toArray($teacher);

            return ($data['decision'] ?? null) === 'rejected'
                && ($data['reason'] ?? null) === 'Calculation mismatch'
                && ($data['grade_id'] ?? null) === $grade->id;
        });

        Notification::assertNotSentTo($studentUser, GradePublished::class);
    }

    public function test_admin_show_lists_only_approved_and_withdrawal_pending_enrollments(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $grade = $this->createPendingGrade()->refresh();
        $subject = $grade->subject;
        $course = $grade->course;
        $teacherId = $grade->graded_by;
        $approvedStudentName = $grade->student->full_name;

        $withdrawalUser = User::factory()->create(['role' => 'student']);
        $withdrawalStudent = Student::create([
            'user_id' => $withdrawalUser->id,
            'student_no' => 'STU'.str_pad((string) $withdrawalUser->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $withdrawalUser->name,
            'email' => $withdrawalUser->email,
            'programme' => 'BSc Computing',
            'intake_year' => '2026',
        ]);
        $withdrawalStudent->courses()->attach($course->id, [
            'status' => 'withdrawal_pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Grade::create([
            'subject_id' => $subject->id,
            'student_id' => $withdrawalStudent->id,
            'graded_by' => $teacherId,
            'score' => 66,
            'status' => Grade::STATUS_PENDING,
        ]);

        $pendingUser = User::factory()->create(['role' => 'student']);
        $pendingStudent = Student::create([
            'user_id' => $pendingUser->id,
            'student_no' => 'STU'.str_pad((string) $pendingUser->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $pendingUser->name,
            'email' => $pendingUser->email,
            'programme' => 'BSc Computing',
            'intake_year' => '2026',
        ]);
        $pendingStudent->courses()->attach($course->id, [
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Grade::create([
            'subject_id' => $subject->id,
            'student_id' => $pendingStudent->id,
            'graded_by' => $teacherId,
            'score' => 71,
            'status' => Grade::STATUS_PENDING,
        ]);

        $response = $this->actingAs($staff)->get(route('admin.grades.show', $subject->id));

        $response->assertOk();
        $response->assertSee($approvedStudentName);
        $response->assertSee($withdrawalStudent->full_name);
        $response->assertDontSee($pendingStudent->full_name);
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
            'student_id' => $student->id,
            'graded_by' => $teacher->id,
            'score' => 78.5,
            'status' => Grade::STATUS_PENDING,
        ]);
    }
}
