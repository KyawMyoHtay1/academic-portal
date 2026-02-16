<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Attendance Threshold (%)
    |--------------------------------------------------------------------------
    |
    | If a student's attendance rate drops strictly below this percentage,
    | they may receive an automated low-attendance alert.
    |
    */
    'low_threshold' => (float) env('ATTENDANCE_LOW_THRESHOLD', 75),

    /*
    |--------------------------------------------------------------------------
    | Alert Cooldown (days)
    |--------------------------------------------------------------------------
    |
    | Minimum days between low-attendance alerts for the same student.
    | This prevents spamming if attendance fluctuates around the threshold.
    |
    */
    'cooldown_days' => (int) env('ATTENDANCE_ALERT_COOLDOWN_DAYS', 7),
];
