<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class TestCase extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'project_id',
        'template_id',
        'type',
        'progress',
        'verdict',
        'client_status',
        'client_comment',
        'source',
        'data',
        'tenant_id',
        'verdict_by',
        'verdict_set_at',
        'progress_by',
        'client_status_by',
    ];

    protected $casts = [
        'data' => 'array',
        'verdict_set_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TestCaseTemplate::class);
    }

    public function verdictBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verdict_by');
    }

    public function progressBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'progress_by');
    }

    public function clientStatusBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_status_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestCaseAssignment::class);
    }

    public function isExecuted(): bool
    {
        return in_array($this->progress, ['termine', 'bloque'], true);
    }

    public function isUat(): bool
    {
        return $this->type === 'uat';
    }

    /**
     * Calcule les statistiques d'un ensemble de cas de test.
     *
     * SOURCE UNIQUE utilisée par Project::stats(), tous les dashboards (chef de
     * projet, testeur) et la génération de rapports. Avant cette réécriture, la
     * même logique existait en 3 versions légèrement différentes (avec des listes
     * de mots-clés incomplètes selon l'endroit), ce qui pouvait faire afficher des
     * pourcentages différents pour les mêmes données selon l'écran consulté.
     *
     * Aucun cas de test n'est "perdu" : total = executed + non_executed, et
     * executed = valide + non_valide + sous_reserve + optimisation + bloque_sans_verdict.
     *
     * @param  Collection<int, TestCase>  $cases
     * @return array{total:int,executed:int,non_executed:int,valide:int,non_valide:int,sous_reserve:int,optimisation:int,bloque:int,taux_execution:float,taux_validation:float}
     */
    public static function statsFor(Collection $cases): array
    {
        $total = $cases->count();
        $executed = $cases->filter(fn (self $case) => $case->isExecuted());

        $counts = [
            'valide' => 0,
            'non_valide' => 0,
            'sous_reserve' => 0,
            'optimisation' => 0,
            'bloque' => 0, // bloqué sans verdict renseigné : ne disparaît plus des stats
        ];

        foreach ($executed as $case) {
            if ($case->progress === 'bloque' && ! $case->verdict) {
                $counts['bloque']++;

                continue;
            }

            if ($case->verdict && array_key_exists($case->verdict, $counts)) {
                $counts[$case->verdict]++;
            }
        }

        $executedCount = $executed->count();

        return [
            'total' => $total,
            'executed' => $executedCount,
            'non_executed' => $total - $executedCount,
            ...$counts,
            'taux_execution' => $total > 0 ? round($executedCount / $total * 100, 1) : 0.0,
            'taux_validation' => $executedCount > 0 ? round($counts['valide'] / $executedCount * 100, 1) : 0.0,
        ];
    }

    /**
     * Statistiques de validation CÔTÉ CLIENT (UAT), distinctes du verdict interne.
     */
    public static function clientStatsFor(Collection $cases): array
    {
        $total = $cases->count();
        $validated = $cases->where('client_status', 'validated')->count();
        $rejected = $cases->where('client_status', 'rejected')->count();
        $pending = $total - $validated - $rejected;

        return [
            'total' => $total,
            'validated' => $validated,
            'rejected' => $rejected,
            'pending' => $pending,
            'taux_approbation' => $total > 0 ? round($validated / $total * 100, 1) : 0.0,
        ];
    }

    /**
     * Répartition des verdicts par auteur (testeur vs chef de projet).
     * Permet au PM de voir ce qui a été fait par les testeurs vs ses propres modifications.
     */
    public static function statsByAuthorBreakdown(Collection $cases): array
    {
        $total = $cases->count();
        $executed = $cases->filter(fn (self $case) => $case->isExecuted());

        $byTester = ['valide' => 0, 'non_valide' => 0, 'sous_reserve' => 0, 'optimisation' => 0, 'bloque' => 0];
        $byChef = ['valide' => 0, 'non_valide' => 0, 'sous_reserve' => 0, 'optimisation' => 0, 'bloque' => 0];

        $chefRoleIds = Role::whereIn('name', ['chef_projet', 'admin'])->pluck('id');

        foreach ($executed as $case) {
            if ($case->progress === 'bloque' && ! $case->verdict) {
                $byTester['bloque']++;

                continue;
            }

            if (! $case->verdict || ! array_key_exists($case->verdict, $byTester)) {
                continue;
            }

            $author = $case->verdictBy;
            if ($author && $author->roles()->whereIn('id', $chefRoleIds)->exists()) {
                $byChef[$case->verdict]++;
            } else {
                $byTester[$case->verdict]++;
            }
        }

        $byTester['total'] = array_sum($byTester);
        $byChef['total'] = array_sum($byChef);

        return [
            'by_tester' => $byTester,
            'by_chef' => $byChef,
        ];
    }
}
