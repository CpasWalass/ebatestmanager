<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestCaseAssignment;

class TestCaseAssignmentPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('assign tests');
    }

    public function delete(User $user, TestCaseAssignment $assignment): bool
    {
        return $user->hasPermissionTo('manage testcases') || $user->id === $assignment->user_id;
    }
}
