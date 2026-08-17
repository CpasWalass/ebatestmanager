<?php

namespace App\Policies;

use App\Models\TestCase;
use App\Models\User;
use App\Support\ProjectAccess;

class TestCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage testcases', 'assign tests', 'view reports', 'validate testcases']);
    }

    public function view(User $user, TestCase $testCase): bool
    {
        if ($user->hasPermissionTo('manage projects') || $user->isChefProjet()) {
            return true;
        }

        return ProjectAccess::isAssignedToProject($user, $testCase->project_id)
            || ProjectAccess::isDeveloperOnProject($user, $testCase->project);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage testcases') && $user->isChefProjet();
    }

    /**
     * Modifier le CONTENU d'un cas de test (scénario, résultats, progress, verdict).
     * Un testeur ne peut le faire que sur un cas qui lui est explicitement assigné —
     * la permission "manage testcases" seule ne suffit plus, elle ne fait
     * qu'autoriser l'accès à l'écran d'édition, pas n'importe quel cas de test.
     */
    public function update(User $user, TestCase $testCase): bool
    {
        if (! $user->hasPermissionTo('manage testcases')) {
            return false;
        }

        if ($user->isChefProjet()) {
            return true;
        }

        return ProjectAccess::isAssignedToProject($user, $testCase->project_id);
    }

    public function delete(User $user, TestCase $testCase): bool
    {
        return $user->hasPermissionTo('manage testcases') && $user->isChefProjet();
    }

    /**
     * Valider/rejeter un cas de test côté client (client_status/client_comment
     * UNIQUEMENT). Séparé de update() : un client ne doit jamais pouvoir modifier
     * le scénario ou le résultat obtenu, seulement se prononcer dessus.
     * Avant cette réécriture, le rôle "client" avait la permission générale
     * "manage testcases", ce qui l'autorisait en théorie à éditer n'importe quel
     * champ de n'importe quel cas de test.
     */
    public function validate(User $user, TestCase $testCase): bool
    {
        if (! $user->hasPermissionTo('validate testcases')) {
            return false;
        }

        return $testCase->isUat() && ProjectAccess::isAssignedToProject($user, $testCase->project_id);
    }
}
