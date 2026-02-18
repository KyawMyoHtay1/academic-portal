<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherGradeDraftOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_cannot_overwrite_another_teachers_draft_via_store(): void
    {
        [$ownerTeacher, $otherTeacher, $student, $subject] = $this->createContextWithTwoTeachers();

        Grade::create([
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'score' => 77,
            'status' => Grade::STATUS_DRAFT,
        ]);

        $this->actingAs($otherTeacher)
            ->post(route('teacher.grades.store', $subject->id), [
                'grades' => [
                    [
                        'student_id' => $student->id,
                        'score' => 88,
                    ],
                ],
            ])
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHas('info');

        $this->assertDatabaseHas('grades', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'status' => Grade::STATUS_DRAFT,
            'score' => 77.00,
        ]);
    }

    public function test_draft_owner_can_update_own_draft_via_store(): void
    {
        [$ownerTeacher, , $student, $subject] = $this->createContextWithTwoTeachers();

        Grade::create([
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'score' => 60,
            'status' => Grade::STATUS_DRAFT,
        ]);

        $this->actingAs($ownerTeacher)
            ->post(route('teacher.grades.store', $subject->id), [
                'grades' => [
                    [
                        'student_id' => $student->id,
                        'score' => 81.5,
                    ],
                ],
            ])
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('grades', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'status' => Grade::STATUS_DRAFT,
            'score' => 81.50,
        ]);
    }

    public function test_teacher_cannot_submit_final_grade_for_another_teachers_draft(): void
    {
        [$ownerTeacher, $otherTeacher, $student, $subject] = $this->createContextWithTwoTeachers();

        Grade::create([
            'subject_id' => $subject->id,
            'course_id' => $subject->course_id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'score' => 74,
            'status' => Grade::STATUS_DRAFT,
        ]);

        $this->actingAs($otherTeacher)
            ->from(route('teacher.grades.show', $subject->id))
            ->post(route('teacher.grades.submit-final', [$subject->id, $student->id]), [
                'use_computed' => false,
                'score' => 90,
            ])
            ->assertRedirect(route('teacher.grades.show', $subject->id))
            ->assertSessionHas('info');

        $this->assertDatabaseHas('grades', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'graded_by' => $ownerTeacher->id,
            'status' => Grade::STATUS_DRAFT,
            'score' => 74.00,
        ]);
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\User, 2: \App\Models\Student, 3: \App\Models\Subject}
     */
    private function createContextWithTwoTeachers(): array
    {
        $ownerTeacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $otherTeacher = User::factory()->create([
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
            'course_code' => 'CSC611',
            'title' => 'Collaborative Grading',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'CG611',
            'title' => 'Collaborative Grading Subject',
            'credits' => 20,
        ]);

        $subject->teachers()->attach([$ownerTeacher->id, $otherTeacher->id], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$ownerTeacher, $otherTeacher, $student, $subject];
    }
}
