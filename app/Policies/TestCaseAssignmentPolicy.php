<?php

namespace App\Policies;

use App\Models\TestCaseAssignment;
use App\Models\User;

class TestCaseAssignmentPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('assign tests');
    }

    public function delete(User $user, TestCaseAssignment $assignment): bool
    {
        return $user->hasPermissionTo('assign tests') || $user->id === $assignment->user_id;
    }
}
