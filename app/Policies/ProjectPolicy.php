<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Support\ProjectAccess;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage projects', 'manage testcases', 'view reports']);
    }

    /**
     * Un chef de projet ou un utilisateur avec "view reports" en général peut voir
     * n'importe quel projet. Un testeur/développeur/client ne peut voir QUE les
     * projets sur lesquels il est explicitement assigné (auparavant vérifié
     * uniquement côté routes web, jamais ici — un appel API aurait pu contourner
     * cette restriction).
     */
    public function view(User $user, Project $project): bool
    {
        if ($user->hasPermissionTo('manage projects')) {
            return true;
        }

        if ($user->hasPermissionTo('manage testcases') || $user->hasPermissionTo('view reports')) {
            // Un chef de projet garde un accès large ; les autres rôles doivent
            // être explicitement rattachés au projet.
            if ($user->isChefProjet()) {
                return true;
            }

            return ProjectAccess::isAssignedToProject($user, $project->id)
                || ProjectAccess::isDeveloperOnProject($user, $project);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage projects');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('manage projects') && $user->id === $project->created_by;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('manage projects') && $user->id === $project->created_by;
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('manage projects');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('manage projects');
    }
}
