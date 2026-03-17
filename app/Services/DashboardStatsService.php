<?php

namespace App\Services;

use App\Models\User;
use App\Services\Dashboard\StaffDashboardDataBuilder;
use App\Services\Dashboard\StudentDashboardDataBuilder;
use App\Services\Dashboard\TeacherDashboardDataBuilder;

class DashboardStatsService
{
    public function __construct(
        private readonly StaffDashboardDataBuilder $staffDashboardDataBuilder,
        private readonly TeacherDashboardDataBuilder $teacherDashboardDataBuilder,
        private readonly StudentDashboardDataBuilder $studentDashboardDataBuilder,
    ) {}

    /**
     * Build dashboard payload for the current role.
     *
     * @return array<string, mixed>
     */
    public function build(?User $user): array
    {
        $cacheTtl = now()->addMinutes(2);

        if ($user?->isStaff()) {
            return $this->staffDashboardDataBuilder->build($cacheTtl);
        }

        if ($user?->isTeacher()) {
            return $this->teacherDashboardDataBuilder->build($user, $cacheTtl);
        }

        return $this->studentDashboardDataBuilder->build($user, $cacheTtl);
    }
}
