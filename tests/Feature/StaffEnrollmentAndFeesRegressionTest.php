<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StaffEnrollmentAndFeesRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_enrollments_index_does_not_require_sort_by_query_param(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.enrollments.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Enrollments/Index')
            ->has('filters')
            ->where('filters.sort_by', 'requested_at')
            ->where('filters.sort_dir', 'desc')
        );
    }

    public function test_staff_fees_index_handles_missing_fee_status_logs_table(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_no' => 'STU-FEE-500',
            'full_name' => 'Fee Regression Student',
            'email' => 'fee-regression@example.test',
            'programme' => 'Computer Science',
            'intake_year' => '2025',
        ]);

        Fee::create([
            'student_id' => $student->id,
            'amount' => 1200.00,
            'description' => 'Semester fee',
            'status' => Fee::STATUS_PENDING,
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        Schema::dropIfExists('fee_status_logs');

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.fees.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Fees/Index')
            ->has('fees.data', 1)
            ->has('fees.data.0.timeline')
        );
    }
}

