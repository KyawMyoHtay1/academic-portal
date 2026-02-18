<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_filter_students_by_search_programme_and_status(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $target = $this->createStudentRecord([
            'student_no' => 'STU1001',
            'full_name' => 'Alice Target',
            'email' => 'alice.target@example.com',
            'programme' => 'BSc (Hons) Computing',
            'intake_year' => '2026',
            'status' => 'active',
        ]);

        $this->createStudentRecord([
            'student_no' => 'STU1002',
            'full_name' => 'Bob Other',
            'email' => 'bob.other@example.com',
            'programme' => 'BBA Business Administration',
            'intake_year' => '2025',
            'status' => 'suspended',
        ]);

        $response = $this->actingAs($staff)->get(route('students.index', [
            'search' => 'STU1001',
            'programme' => 'BSc (Hons) Computing',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame('Students/Index', $page['component']);

        $students = collect($page['props']['students']['data'] ?? []);
        $this->assertCount(1, $students);
        $this->assertSame($target->id, $students->first()['id']);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function createStudentRecord(array $attributes): Student
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email' => $attributes['email'],
            'name' => $attributes['full_name'],
        ]);

        return Student::create([
            'user_id' => $user->id,
            'student_no' => $attributes['student_no'],
            'full_name' => $attributes['full_name'],
            'email' => $attributes['email'],
            'programme' => $attributes['programme'],
            'intake_year' => $attributes['intake_year'],
            'status' => $attributes['status'],
        ]);
    }
}

