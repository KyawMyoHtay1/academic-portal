<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class FailedJobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_failed_jobs_index(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $failedJobId = $this->createFailedJobRecord();

        $response = $this
            ->actingAs($staff)
            ->get(route('admin.failed-jobs.index'));

        $response->assertOk();
        $response->assertSee((string) $failedJobId);
    }

    public function test_staff_can_retry_a_failed_job(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $failedJobId = $this->createFailedJobRecord();

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.failed-jobs.retry', $failedJobId));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('failed_jobs', [
            'uuid' => $failedJobId,
        ]);
    }

    public function test_non_staff_cannot_access_failed_job_admin_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $failedJobId = $this->createFailedJobRecord();

        $this->actingAs($teacher)
            ->get(route('admin.failed-jobs.index'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('admin.failed-jobs.retry', $failedJobId))
            ->assertForbidden();
    }

    public function test_staff_can_delete_a_failed_job(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $failedJobId = $this->createFailedJobRecord();

        $response = $this
            ->actingAs($staff)
            ->delete(route('admin.failed-jobs.destroy', $failedJobId));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('failed_jobs', [
            'uuid' => $failedJobId,
        ]);
    }

    private function createFailedJobRecord(): string
    {
        $uuid = (string) Str::uuid();

        DB::table('failed_jobs')->insert([
            'uuid' => $uuid,
            'connection' => 'sync',
            'queue' => 'default',
            'payload' => json_encode([
                'uuid' => $uuid,
                'displayName' => 'App\\Jobs\\SendLowAttendanceAlertsJob',
                'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                'data' => [],
            ], JSON_THROW_ON_ERROR),
            'exception' => 'RuntimeException: Simulated queue failure',
            'failed_at' => now(),
        ]);

        return $uuid;
    }
}
