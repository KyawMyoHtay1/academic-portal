<?php

namespace Tests\Unit;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\SubjectGradeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectGradeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_subject_grades_for_multiple_students_in_batch(): void
    {
        [$teacher, $subject, $course, $studentA, $studentB] = $this->createContext();

        $assignmentOne = Assignment::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Assignment 1',
            'description' => 'Task 1',
            'due_date' => now()->addDays(2)->toDateString(),
            'max_score' => 100,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        $assignmentTwo = Assignment::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Assignment 2',
            'description' => 'Task 2',
            'due_date' => now()->addDays(4)->toDateString(),
            'max_score' => 50,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentOne->id,
            'student_id' => $studentA->id,
            'file_path' => 'submissions/a1-a.pdf',
            'original_filename' => 'a1-a.pdf',
            'score' => 80,
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentTwo->id,
            'student_id' => $studentA->id,
            'file_path' => 'submissions/a2-a.pdf',
            'original_filename' => 'a2-a.pdf',
            'score' => 25,
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_RETURNED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentOne->id,
            'student_id' => $studentB->id,
            'file_path' => 'submissions/a1-b.pdf',
            'original_filename' => 'a1-b.pdf',
            'score' => 0,
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentTwo->id,
            'student_id' => $studentB->id,
            'file_path' => 'submissions/a2-b.pdf',
            'original_filename' => 'a2-b.pdf',
            'status' => AssignmentSubmission::STATUS_SUBMITTED,
        ]);

        $calculator = new SubjectGradeCalculator();
        $result = $calculator->calculateForSubjectStudents($subject->id, [$studentA->id, $studentB->id]);

        $this->assertArrayHasKey($studentA->id, $result);
        $this->assertArrayHasKey($studentB->id, $result);

        $this->assertEquals(65.0, $result[$studentA->id]['computed_grade']);
        $this->assertSame(2, $result[$studentA->id]['graded_assignments']);
        $this->assertSame(2, $result[$studentA->id]['total_assignments']);
        $this->assertSame($teacher->name, $result[$studentA->id]['breakdown'][0]['graded_by']);

        $this->assertEquals(0.0, $result[$studentB->id]['computed_grade']);
        $this->assertSame(1, $result[$studentB->id]['graded_assignments']);
        $this->assertSame(2, $result[$studentB->id]['total_assignments']);
        $this->assertTrue($result[$studentB->id]['breakdown'][1]['submitted']);
        $this->assertFalse($result[$studentB->id]['breakdown'][1]['graded']);
        $this->assertNull($result[$studentB->id]['breakdown'][1]['graded_by']);
    }

    public function test_it_calculates_subject_breakdown_for_one_student_across_many_subjects(): void
    {
        [$teacher, $subjectOne, $course, $studentA] = $this->createContextWithSingleStudent();

        $subjectTwo = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'PHY301',
            'title' => 'Physics for Computing',
            'credits' => 20,
        ]);

        $subjectTwo->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $assignmentOne = Assignment::create([
            'subject_id' => $subjectOne->id,
            'created_by' => $teacher->id,
            'title' => 'SE Quiz',
            'description' => 'Quiz',
            'due_date' => now()->addDay()->toDateString(),
            'max_score' => 20,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        $assignmentTwo = Assignment::create([
            'subject_id' => $subjectTwo->id,
            'created_by' => $teacher->id,
            'title' => 'Physics Quiz',
            'description' => 'Quiz',
            'due_date' => now()->addDays(2)->toDateString(),
            'max_score' => 40,
            'status' => Assignment::STATUS_PUBLISHED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentOne->id,
            'student_id' => $studentA->id,
            'file_path' => 'submissions/se-quiz.pdf',
            'original_filename' => 'se-quiz.pdf',
            'score' => 10,
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignmentTwo->id,
            'student_id' => $studentA->id,
            'file_path' => 'submissions/physics-quiz.pdf',
            'original_filename' => 'physics-quiz.pdf',
            'score' => 30,
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        $calculator = new SubjectGradeCalculator();
        $result = $calculator->calculateForStudentSubjects([$subjectOne->id, $subjectTwo->id], $studentA->id);

        $this->assertArrayHasKey($subjectOne->id, $result);
        $this->assertArrayHasKey($subjectTwo->id, $result);
        $this->assertEquals(50.0, $result[$subjectOne->id]['computed_grade']);
        $this->assertEquals(75.0, $result[$subjectTwo->id]['computed_grade']);
        $this->assertSame(1, $result[$subjectOne->id]['total_assignments']);
        $this->assertSame(1, $result[$subjectTwo->id]['total_assignments']);
        $this->assertSame($teacher->name, $result[$subjectOne->id]['breakdown'][0]['graded_by']);
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Subject, 2: \App\Models\Course, 3: \App\Models\Student, 4: \App\Models\Student}
     */
    private function createContext(): array
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::create([
            'course_code' => 'CSC401',
            'title' => 'Distributed Systems',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'DS401',
            'title' => 'Distributed Systems Engineering',
            'credits' => 20,
        ]);

        $subject->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        [, $studentA] = $this->createStudent('student-a@example.com');
        [, $studentB] = $this->createStudent('student-b@example.com');

        $studentA->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentB->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$teacher, $subject, $course, $studentA, $studentB];
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Subject, 2: \App\Models\Course, 3: \App\Models\Student}
     */
    private function createContextWithSingleStudent(): array
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::create([
            'course_code' => 'CSC501',
            'title' => 'Applied Computing',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'APP501',
            'title' => 'Applied Software',
            'credits' => 20,
        ]);

        $subject->teachers()->attach($teacher->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        [, $student] = $this->createStudent('student-c@example.com');
        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$teacher, $subject, $course, $student];
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Student}
     */
    private function createStudent(string $email): array
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email' => $email,
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
