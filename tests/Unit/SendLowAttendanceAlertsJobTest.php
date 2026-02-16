<?php

namespace Tests\Unit;

use App\Jobs\SendLowAttendanceAlertsJob;
use Carbon\CarbonInterface;
use Tests\TestCase;

class SendLowAttendanceAlertsJobTest extends TestCase
{
    public function test_job_retry_policy_is_hardened(): void
    {
        $job = new SendLowAttendanceAlertsJob;

        $this->assertSame(5, $job->tries);
        $this->assertSame(3, $job->maxExceptions);
        $this->assertSame([60, 300, 900, 1800], $job->backoff);
        $this->assertSame(120, $job->timeout);

        $retryUntil = $job->retryUntil();
        $this->assertInstanceOf(CarbonInterface::class, $retryUntil);
        $this->assertTrue($retryUntil->greaterThan(now()->addHours(5)));
    }
}
