<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestCaseTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'project_id',
        'name',
        'fields',
        'tenant_id',
        'links',
    ];

    protected $casts = [
        'fields' => 'array',
        'links' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(TestCase::class, 'template_id');
    }

    /**
     * Champs dynamiques par défaut d'un template.
     *
     * IMPORTANT : "ETAT DE TEST" et "STATUS" ne font PLUS partie de ces champs
     * libres. Ils vivent désormais dans des colonnes dédiées sur test_cases
     * (progress / verdict), pilotées par des contrôles fixes de l'interface,
     * pour ne plus dépendre d'une reconnaissance de texte fragile. Ça évite
     * aussi d'avoir deux endroits différents où éditer le statut d'un même cas.
     */
    public static function defaultFields(): array
    {
        return [
            ['name' => 'cas_test', 'label' => 'CAS DE TEST', 'type' => 'text', 'required' => true],
            ['name' => 'modules', 'label' => 'MODULES', 'type' => 'text', 'required' => false],
            ['name' => 'fonctionnalites', 'label' => 'FONCTIONNALITES', 'type' => 'text', 'required' => false],
            ['name' => 'scenarios_test', 'label' => 'SCENARIOS DE TEST', 'type' => 'textarea', 'required' => true],
            ['name' => 'resultats_attendus', 'label' => 'RESULTATS ATTENDUS', 'type' => 'textarea', 'required' => true],
            ['name' => 'resultats_obtenus', 'label' => 'RESULTATS OBTENUS', 'type' => 'textarea', 'required' => false],
            [
                'name' => 'nature',
                'label' => 'NATURE',
                'type' => 'select',
                'required' => false,
                'options' => [
                    'Concluant',
                    'Erreurs Fonctionnelles',
                    'Erreurs de Validation / Saisie',
                    "Erreurs d'Interface (UI/UX)",
                    'Erreurs Techniques',
                    'Erreurs de Performance',
                    'Erreurs de Sécurité',
                    'Erreurs de Données',
                    "Erreurs d'Intégration",
                    'Erreurs de Compatibilité',
                    'Erreurs de Workflow / Navigation',
                ],
                'option_colors' => [
                    'Concluant' => '#22c55e',
                    'Erreurs Fonctionnelles' => '#ef4444',
                    'Erreurs de Validation / Saisie' => '#ef4444',
                    "Erreurs d'Interface (UI/UX)" => '#ef4444',
                    'Erreurs Techniques' => '#ef4444',
                    'Erreurs de Performance' => '#ef4444',
                    'Erreurs de Sécurité' => '#ef4444',
                    'Erreurs de Données' => '#ef4444',
                    "Erreurs d'Intégration" => '#ef4444',
                    'Erreurs de Compatibilité' => '#ef4444',
                    'Erreurs de Workflow / Navigation' => '#ef4444',
                ],
            ],
            ['name' => 'commentaires', 'label' => 'COMMENTAIRES', 'type' => 'textarea', 'required' => false],
        ];
    }

    /**
     * Normalise un texte pour comparaison insensible aux accents/casse
     * (utilisé pour faire correspondre les en-têtes/valeurs d'un fichier Excel
     * importé aux valeurs attendues de progress/verdict).
     */
    public static function normalizeLabel(string $str): string
    {
        $str = mb_strtolower(trim($str));
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str) ?: $str;

        return preg_replace('/[^a-z0-9]/', '', $str) ?? '';
    }

    /**
     * Résout un libellé libre ('Terminé', 'terminé ', 'DONE'...) vers la valeur
     * enum progress correspondante, ou null si aucune correspondance.
     */
    public static function resolveProgressValue(string $label): ?string
    {
        $normalized = self::normalizeLabel($label);

        $aliases = [
            'afaire' => 'a_faire', 'todo' => 'a_faire', 'enattente' => 'a_faire',
            'encours' => 'en_cours', 'inprogress' => 'en_cours', 'pending' => 'en_cours',
            'bloque' => 'bloque', 'blocked' => 'bloque',
            'termine' => 'termine', 'done' => 'termine', 'complete' => 'termine', 'ok' => 'termine',
        ];

        return $aliases[$normalized] ?? null;
    }

    /**
     * Résout un libellé libre ('Validé', 'OK', 'PASS'...) vers la valeur enum
     * verdict correspondante, ou null si aucune correspondance.
     */
    public static function resolveVerdictValue(string $label): ?string
    {
        $normalized = self::normalizeLabel($label);

        $aliases = [
            'valide' => 'valide', 'ok' => 'valide', 'passed' => 'valide', 'pass' => 'valide', 'concluant' => 'valide', 'succes' => 'valide',
            'nonvalide' => 'non_valide', 'ko' => 'non_valide', 'failed' => 'non_valide', 'fail' => 'non_valide', 'echec' => 'non_valide', 'rejete' => 'non_valide',
            'sousreserve' => 'sous_reserve', 'reserve' => 'sous_reserve',
            'optimisation' => 'optimisation', 'amelioration' => 'optimisation',
        ];

        return $aliases[$normalized] ?? null;
    }

    /**
     * Options du champ "nature" (libellés exacts, source unique).
     * 'Concluant' vient en premier car c'est le cas nominal d'un test réussi.
     */
    public static function natureOptions(): array
    {
        return array_merge(['Concluant'], TestExecution::$natures);
    }

    /**
     * Résout une valeur libre importée depuis un Excel ('Fonctionnel', 'BUG',
     * 'Erreur interface'...) vers le libellé exact d'une option de nature,
     * ou null si aucune correspondance satisfaisante.
     */
    public static function resolveNatureValue(string $label): ?string
    {
        $normalized = self::normalizeLabel($label);
        if ($normalized === '') {
            return null;
        }

        $bestOption = null;
        $bestScore = 0;

        foreach (self::natureOptions() as $option) {
            $optionNormalized = self::normalizeLabel($option);

            if ($optionNormalized === $normalized) {
                return $option;
            }

            if (str_contains($optionNormalized, $normalized) || str_contains($normalized, $optionNormalized)) {
                $score = min(strlen($normalized), strlen($optionNormalized));

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestOption = $option;
                }
            }
        }

        // Correspondance par mots-clés pour les libellés courts type "BUG", "PERF"...
        $keywords = [
            'fonctionnel' => 'Erreurs Fonctionnelles',
            'validation' => 'Erreurs de Validation / Saisie',
            'saisie' => 'Erreurs de Validation / Saisie',
            'interface' => "Erreurs d'Interface (UI/UX)",
            'ui' => "Erreurs d'Interface (UI/UX)",
            'ux' => "Erreurs d'Interface (UI/UX)",
            'technique' => 'Erreurs Techniques',
            'performance' => 'Erreurs de Performance',
            'perf' => 'Erreurs de Performance',
            'securite' => 'Erreurs de Sécurité',
            'donnees' => 'Erreurs de Données',
            'integration' => "Erreurs d'Intégration",
            'compatibilite' => 'Erreurs de Compatibilité',
            'workflow' => 'Erreurs de Workflow / Navigation',
            'navigation' => 'Erreurs de Workflow / Navigation',
            'concluant' => 'Concluant',
            'ok' => 'Concluant',
            'bug' => 'Erreurs Fonctionnelles',
            'erreur' => 'Erreurs Fonctionnelles',
        ];

        foreach ($keywords as $kw => $mapping) {
            if (str_contains($normalized, $kw)) {
                return $mapping;
            }
        }

        return $bestScore > 0 ? $bestOption : null;
    }

    /**
     * Libellés + couleurs pour l'axe "progress", utilisés partout dans l'UI
     * (badges, filtres, exports) pour ne jamais désynchroniser vocabulaire et couleurs.
     */
    public static function progressOptions(): array
    {
        return [
            'a_faire' => ['label' => 'À faire', 'color' => '#6b7280'],
            'en_cours' => ['label' => 'En cours', 'color' => '#f97316'],
            'bloque' => ['label' => 'Bloqué', 'color' => '#ef4444'],
            'termine' => ['label' => 'Terminé', 'color' => '#22c55e'],
        ];
    }

    /**
     * Libellés + couleurs pour l'axe "verdict".
     */
    public static function verdictOptions(): array
    {
        return [
            'valide' => ['label' => 'Validé', 'color' => '#22c55e'],
            'non_valide' => ['label' => 'Non validé', 'color' => '#ef4444'],
            'sous_reserve' => ['label' => 'Sous réserve', 'color' => '#a855f7'],
            'optimisation' => ['label' => 'Optimisation', 'color' => '#f97316'],
        ];
    }
}
