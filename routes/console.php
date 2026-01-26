<?php

use App\Jobs\SendLowAttendanceAlertsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:send-low-attendance-alerts', function () {
    SendLowAttendanceAlertsJob::dispatch();
    $this->info('Dispatched low attendance alerts job.');
})->purpose('Send automated low attendance alerts to students');
