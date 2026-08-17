<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage clients', 'view reports']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyPermission(['manage clients', 'view reports']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage clients');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('manage clients');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('manage clients');
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('manage clients');
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('manage clients');
    }
}
