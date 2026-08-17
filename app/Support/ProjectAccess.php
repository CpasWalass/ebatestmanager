<?php

namespace App\Support;

use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;

/**
 * Avant cette réécriture, la question "cet utilisateur peut-il accéder à ce
 * projet / ce template ?" pour un testeur, un développeur ou un client était
 * répondue par un bloc de code dupliqué (presque à l'identique) directement
 * dans routes/web.php, à 3 endroits différents. Ici : un seul endroit,
 * utilisé à la fois par les Policies et les composants Livewire.
 */
class ProjectAccess
{
    public static function isAssignedToProject(User $user, int $projectId): bool
    {
        return TestCaseAssignment::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->exists();
    }

    public static function isAssignedToTemplate(User $user, TestCaseTemplate $template): bool
    {
        return TestCaseAssignment::where('user_id', $user->id)
            ->where('template_id', $template->id)
            ->exists()
            || self::isAssignedToProject($user, $template->project_id);
    }

    public static function isDeveloperOnProject(User $user, Project $project): bool
    {
        return $project->developers()->where('users.id', $user->id)->exists();
    }
}
