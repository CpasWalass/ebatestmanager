<?php

namespace App\Policies;

use App\Models\TestCaseTemplate;
use App\Models\User;
use App\Support\ProjectAccess;

class TestCaseTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage testcases', 'view reports']);
    }

    public function view(User $user, TestCaseTemplate $template): bool
    {
        if ($user->isChefProjet() || $user->hasPermissionTo('manage projects')) {
            return true;
        }

        return $user->hasAnyPermission(['manage testcases', 'view reports'])
            && ProjectAccess::isAssignedToTemplate($user, $template);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage testcases') && $user->isChefProjet();
    }

    public function update(User $user, TestCaseTemplate $template): bool
    {
        return $user->hasPermissionTo('manage testcases') && $user->isChefProjet();
    }

    public function delete(User $user, TestCaseTemplate $template): bool
    {
        return $user->hasPermissionTo('manage testcases') && $user->isChefProjet();
    }
}
