<?php

namespace App\Support;

use App\Models\SystemSetting;

class AttendanceAlertSettings
{
    public const KEY_LOW_THRESHOLD = 'attendance.low_threshold';

    public const KEY_COOLDOWN_DAYS = 'attendance.cooldown_days';

    public static function lowThreshold(): float
    {
        $default = (float) config('attendance_alerts.low_threshold', 75);
        $value = (float) SystemSetting::getValue(self::KEY_LOW_THRESHOLD, $default);

        return max(1, min(100, $value));
    }

    public static function cooldownDays(): int
    {
        $default = (int) config('attendance_alerts.cooldown_days', 7);
        $value = (int) SystemSetting::getValue(self::KEY_COOLDOWN_DAYS, $default);

        return max(0, min(90, $value));
    }

    public static function setLowThreshold(float $value, ?int $updatedBy = null): void
    {
        $normalized = max(1, min(100, round($value, 2)));
        SystemSetting::setValue(self::KEY_LOW_THRESHOLD, $normalized, $updatedBy);
    }

    public static function setCooldownDays(int $value, ?int $updatedBy = null): void
    {
        $normalized = max(0, min(90, $value));
        SystemSetting::setValue(self::KEY_COOLDOWN_DAYS, $normalized, $updatedBy);
    }
}
