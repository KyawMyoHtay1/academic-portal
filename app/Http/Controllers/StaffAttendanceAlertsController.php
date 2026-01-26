<?php

namespace App\Http\Controllers;

use App\Jobs\SendLowAttendanceAlertsJob;
use Illuminate\Http\RedirectResponse;

class StaffAttendanceAlertsController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        SendLowAttendanceAlertsJob::dispatch();

        return back()->with('success', 'Low attendance alerts have been queued.');
    }
}

