<?php

namespace Tests\Unit;

use App\Jobs\SendLowAttendanceAlertsJob;
use App\Models\LowAttendanceAlertState;
use App\Models\Student;
use App\Models\User;
use App\Notifications\LowAttendanceAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendLowAttendanceAlertsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_completes_when_no_attendance_data(): void
    {
        $job = new SendLowAttendanceAlertsJob;
        $job->handle();

        $this->assertTrue(true);
    }

    public function test_handle_sends_alerts_for_selected_student_snapshots(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => 'student',
            'preferences' => [
                'notify_attendance' => true,
                'email_notifications' => true,
            ],
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_no' => 'STU5001',
            'programme' => 'Computer Science',
            'intake_year' => '2025',
        ]);

        LowAttendanceAlertState::create([
            'student_id' => $student->id,
            'last_rate' => 88.5,
            'is_below_threshold' => false,
        ]);

        $job = new SendLowAttendanceAlertsJob(
            thresholdOverride: 75.0,
            cooldownDaysOverride: 0,
            studentSnapshots: [[
                'student_id' => $student->id,
                'rate' => 50.0,
            ]]
        );

        $job->handle();

        Notification::assertSentTo($user, LowAttendanceAlert::class);
        $this->assertDatabaseHas('low_attendance_alert_states', [
            'student_id' => $student->id,
            'last_rate' => '50.00',
            'is_below_threshold' => 1,
        ]);
    }
}
