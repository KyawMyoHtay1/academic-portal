<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffUserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_search_users_by_role_keyword(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'name' => 'Staff Manager',
            'email' => 'staff.manager@example.com',
        ]);

        $teacher = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Alice Lecturer',
            'email' => 'alice.lecturer@example.com',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Bob Student',
            'email' => 'bob.student@example.com',
        ]);

        $response = $this->actingAs($staff)->get(route('admin.users.index', [
            'search' => 'teacher',
        ]));

        $response->assertOk();
        $response->assertViewHas('page');

        $page = $response->viewData('page');
        $this->assertSame('Admin/Users/Index', $page['component']);

        $users = collect($page['props']['users']['data'] ?? []);
        $userIds = $users->pluck('id')->all();

        $this->assertContains($teacher->id, $userIds);
        $this->assertNotContains($student->id, $userIds);
    }
}

