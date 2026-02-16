<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function enroll(User $user, Course $course): bool
    {
        return $user->isStudent() && $user->student !== null;
    }

    public function unenroll(User $user, Course $course): bool
    {
        return $user->isStudent() && $user->student !== null;
    }
}
