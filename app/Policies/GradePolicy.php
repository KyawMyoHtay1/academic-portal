<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->isTeacher() || $user->isStaff();
    }

    public function view(User $user, Grade $grade): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        if ($user->isStudent()) {
            return $user->student?->id === $grade->student_id;
        }

        if ($user->isTeacher()) {
            return $user->teachingSubjects()
                ->where('subjects.id', $grade->subject_id)
                ->exists();
        }

        return false;
    }

    public function review(User $user, Grade $grade): bool
    {
        return $user->isStaff();
    }
}
