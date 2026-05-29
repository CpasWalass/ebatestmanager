<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestCaseTemplate;

class TestCaseTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage testcases') || $user->hasPermissionTo('view reports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TestCaseTemplate $template): bool
    {
        return $user->hasPermissionTo('manage testcases') || $user->hasPermissionTo('view reports');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage testcases');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TestCaseTemplate $template): bool
    {
        return $user->hasPermissionTo('manage testcases');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TestCaseTemplate $template): bool
    {
        return $user->hasPermissionTo('manage testcases');
    }
}
