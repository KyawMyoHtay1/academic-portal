<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->isStaff();
    }

    public function view(User $user, Fee $fee): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $user->isStudent() && $user->student?->id === $fee->student_id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Fee $fee): bool
    {
        return $user->isStaff();
    }

    public function delete(User $user, Fee $fee): bool
    {
        return $user->isStaff();
    }

    public function submitPayment(User $user, Fee $fee): bool
    {
        return $user->isStudent() && $user->student?->id === $fee->student_id;
    }

    public function checkout(User $user, Fee $fee): bool
    {
        return $this->submitPayment($user, $fee);
    }

    public function approvePayment(User $user, Fee $fee): bool
    {
        return $user->isStaff();
    }

    public function rejectPayment(User $user, Fee $fee): bool
    {
        return $user->isStaff();
    }

    public function receipt(User $user, Fee $fee): bool
    {
        return $user->isStaff();
    }
}

