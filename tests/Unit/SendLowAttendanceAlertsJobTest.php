<?php

namespace Tests\Unit;

use App\Jobs\SendLowAttendanceAlertsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
