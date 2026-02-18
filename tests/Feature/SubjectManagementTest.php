<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_assign_non_teacher_user_to_subject(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $nonTeacher = User::factory()->create([
            'role' => 'student',
        ]);

        $course = Course::create([
            'course_code' => 'CSC410',
            'title' => 'Architecture',
            'credits' => 20,
            'semester' => 'Semester 1',
        ]);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'ARC410',
            'title' => 'Software Architecture',
            'credits' => 20,
        ]);

        $this
            ->actingAs($staff)
            ->from(route('admin.subjects.assign-teachers', $subject))
            ->put(route('admin.subjects.assign-teachers.update', $subject), [
                'teacher_ids' => [$nonTeacher->id],
            ])
            ->assertRedirect(route('admin.subjects.assign-teachers', $subject))
            ->assertSessionHasErrors('teacher_ids.0');

        $this->assertDatabaseMissing('subject_teacher', [
            'subject_id' => $subject->id,
            'user_id' => $nonTeacher->id,
        ]);
    }

    public function test_deleting_subject_removes_associated_photo_file(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $course = Course::create([
            'course_code' => 'CSC420',
            'title' => 'Security',
            'credits' => 20,
            'semester' => 'Semester 2',
        ]);

        $photoPath = 'subjects/test-subject-photo.jpg';
        Storage::disk('public')->put($photoPath, 'fake-image-content');
        Storage::disk('public')->assertExists($photoPath);

        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'SEC420',
            'title' => 'Security Engineering',
            'credits' => 20,
            'photo' => $photoPath,
        ]);

        $this
            ->actingAs($staff)
            ->delete(route('admin.subjects.destroy', $subject))
            ->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
        Storage::disk('public')->assertMissing($photoPath);
    }
}

