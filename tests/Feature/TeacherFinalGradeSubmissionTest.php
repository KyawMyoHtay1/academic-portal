<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\GradeReviewRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TeacherFinalGradeSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_submit_final_grade_using_computed_score(): void
    {
        Notification::fake();

        [$teacher, $staff, $student, $subject, $course] = $this->createTeacherGradeContext();

        $assignment = Assignment::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Essay 1',
            'description' => 'Essay',
            'due_date' => now()->addWeek()->toDateString(),
            'max_score' => 50,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => 'submissions/essay-1.pdf',
            'original_filename' => 'essay-1.pdf',
            'score' => 45,
            'feedback' => 'Good',
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->post(route('teacher.grades.submit-final', [$subject->id, $student->id]), [
                'use_computed' => true,
            ]);

        $response
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHas('success');

        $grade = Grade::where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->first();

        $this->assertNotNull($grade);
        $this->assertSame(Grade::STATUS_PENDING, $grade->status);
        $this->assertSame($teacher->id, $grade->graded_by);
        $this->assertEquals(90.0, (float) $grade->score);

        $log = GradeReviewLog::where('grade_id', $grade->id)->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertSame('submitted', $log->action);
        $this->assertTrue((bool) ($log->meta['use_computed'] ?? false));
        $this->assertEquals(90.0, (float) ($log->meta['computed_score'] ?? -1));

        Notification::assertSentTo($staff, GradeReviewRequested::class);
    }

    public function test_teacher_must_provide_manual_score_when_not_using_computed(): void
    {
        [$teacher, , $student, $subject] = $this->createTeacherGradeContext();

        $response = $this
            ->actingAs($teacher)
            ->from(route('teacher.grades.show', $subject->id))
            ->post(route('teacher.grades.submit-final', [$subject->id, $student->id]), [
                'use_computed' => false,
            ]);

        $response
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHasErrors('score');

        $this->assertDatabaseMissing('grades', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_teacher_cannot_use_computed_score_when_no_assignments_are_graded(): void
    {
        [$teacher, , $student, $subject, $course] = $this->createTeacherGradeContext();

        $assignment = Assignment::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Quiz 1',
            'description' => 'Quiz',
            'due_date' => now()->addWeek()->toDateString(),
            'max_score' => 20,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => 'submissions/quiz-1.pdf',
            'original_filename' => 'quiz-1.pdf',
            'status' => AssignmentSubmission::STATUS_SUBMITTED,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->from(route('teacher.grades.show', $subject->id))
            ->post(route('teacher.grades.submit-final', [$subject->id, $student->id]), [
                'use_computed' => true,
            ]);

        $response
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHasErrors('score');

        $this->assertDatabaseMissing('grades', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
        ]);
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\User, 2: \App\Models\Student, 3: \App\Models\Subject, 4: \App\Models\Course}
     */
    private function createTeacherGradeContext(): array
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
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
            'course_code' => 'CSC301',
            'title' => 'Software Engineering',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SE301',
            'title' => 'Software Engineering Practice',
            'credits' => 20,
        ]);

        $subject->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$teacher, $staff, $student, $subject, $course];
    }
}
