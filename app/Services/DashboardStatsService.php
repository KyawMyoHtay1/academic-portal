<?php

namespace App\Services;

use App\Models\User;
use App\Services\Dashboard\StaffDashboardDataBuilder;
use App\Services\Dashboard\StudentDashboardDataBuilder;
use App\Services\Dashboard\TeacherDashboardDataBuilder;

class DashboardStatsService
{
    public function __construct(
        private readonly StaffDashboardDataBuilder ,
        private readonly TeacherDashboardDataBuilder ,
        private readonly StudentDashboardDataBuilder ,
    ) {}

    /**
     * Build dashboard payload for the current role.
     *
     * @return array<string, mixed>
     */
    public function build(?User ): array
    {
         = now()->addMinutes(2);

        if (->isStaff()) {
            return ->staffDashboardDataBuilder->build();
        }

        if (->isTeacher()) {
            return ->teacherDashboardDataBuilder->build(, );
        }

        return ->studentDashboardDataBuilder->build(, );
    }
}
