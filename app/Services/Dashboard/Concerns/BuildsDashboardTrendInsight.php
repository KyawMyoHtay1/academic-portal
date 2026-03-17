<?php

namespace App\Services\Dashboard\Concerns;

trait BuildsDashboardTrendInsight
{
    /**
     * @return array<string, float|string>
     */
    protected function buildTrendInsight(float $current, float $previous): array
    {
        $delta = round($current - $previous, 1);
        $direction = 'flat';
        if ($delta > 0) {
            $direction = 'up';
        } elseif ($delta < 0) {
            $direction = 'down';
        }

        if ($previous > 0) {
            $percent = round(($delta / $previous) * 100, 1);
        } elseif ($current > 0) {
            $percent = 100.0;
        } else {
            $percent = 0.0;
        }

        return [
            'current' => round($current, 1),
            'previous' => round($previous, 1),
            'delta' => $delta,
            'percent' => $percent,
            'direction' => $direction,
        ];
    }
}
