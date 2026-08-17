<?php

namespace App\Support;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestExecution;

/**
 * Formatage des entrées du journal d'activité (Spatie Activitylog).
 *
 * Les anciennes entrées en base stockent les descriptions Spatie en anglais
 * ("Ce projet a été updated"). On normalise ici l'affichage en français, et on
 * résout le nom de la cible (nom du projet / du cas de test au lieu de l'ID).
 */
class ActivityLogHelper
{
    /**
     * Verbe d'action en français pour une description d'événement standard
     * (ancien format anglais ou nouveau format français). Retourne null pour
     * les messages personnalisés (déjà en français) à afficher tels quels.
     */
    public static function verb(?string $description): ?string
    {
        return match ($description) {
            'created',
            'Ce projet a été created',
            'Ce projet a été créé',
            'Cette exécution a été created',
            'Cette exécution a été créée' => 'créé',

            'updated',
            'Ce projet a été updated',
            'Ce projet a été mis à jour',
            'Cette exécution a été updated',
            'Cette exécution a été mise à jour' => 'mis à jour',

            'deleted',
            'Ce projet a été deleted',
            'Ce projet a été supprimé',
            'Cette exécution a été deleted',
            'Cette exécution a été supprimée' => 'supprimé',

            'restored',
            'Ce projet a été restored',
            'Ce projet a été restauré',
            'Cette exécution a été restored',
            'Cette exécution a été restaurée' => 'restauré',

            default => null,
        };
    }

    /**
     * Nom lisible de la cible d'une action. Pour un projet, son nom. Pour un
     * cas de test (ou une exécution), le nom du cas si disponible.
     */
    public static function subjectName(?string $subjectType, ?int $subjectId): ?string
    {
        if (! $subjectType || ! $subjectId) {
            return null;
        }

        if ($subjectType === Project::class) {
            $project = Project::withTrashed()->find($subjectId);

            return $project?->name;
        }

        if ($subjectType === TestCase::class) {
            $case = TestCase::find($subjectId);

            return self::caseName($case);
        }

        if ($subjectType === TestExecution::class) {
            $execution = TestExecution::find($subjectId);
            $caseName = self::caseName($execution?->testCase);

            return $caseName ? "Cas de test « {$caseName} »" : null;
        }

        return null;
    }

    private static function caseName(?TestCase $case): ?string
    {
        if (! $case) {
            return null;
        }

        return $case->data['cas_test']
            ?? $case->data['test_case']
            ?? $case->data['titre_cas_test']
            ?? $case->data['titre']
            ?? null;
    }
}
