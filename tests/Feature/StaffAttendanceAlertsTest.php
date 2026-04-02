<?php

namespace Tests\Feature;

use App\Jobs\SendLowAttendanceAlertsJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use ReflectionProperty;
use Tests\TestCase;

class StaffAttendanceAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_run_bypasses_cooldown_by_default(): void
    {
        Bus::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.attendance.alerts.run'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Bus::assertDispatched(SendLowAttendanceAlertsJob::class, function (SendLowAttendanceAlertsJob $job) {
            $cooldown = new ReflectionProperty($job, 'cooldownDaysOverride');
            $cooldown->setAccessible(true);

            $threshold = new ReflectionProperty($job, 'thresholdOverride');
            $threshold->setAccessible(true);

            return $threshold->getValue($job) === null
                && $cooldown->getValue($job) === 0;
        });
    }

    public function test_manual_run_respects_explicit_cooldown_override(): void
    {
        Bus::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('admin.attendance.alerts.run'), [
                'threshold' => 68.5,
                'cooldown_days' => 4,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Bus::assertDispatched(SendLowAttendanceAlertsJob::class, function (SendLowAttendanceAlertsJob $job) {
            $cooldown = new ReflectionProperty($job, 'cooldownDaysOverride');
            $cooldown->setAccessible(true);

            $threshold = new ReflectionProperty($job, 'thresholdOverride');
            $threshold->setAccessible(true);

            return $threshold->getValue($job) === 68.5
                && $cooldown->getValue($job) === 4;
        });
    }
}
