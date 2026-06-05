<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestExecution extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'test_case_id',
        'tester_id',
        'assignment_id',
        'results',
        'status',
        'comments',
        'nature',
        'priority',
        'started_at',
        'completed_at',
        'tenant_id',
    ];

    protected $casts = [
        'results'      => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Libellés des statuts
     */
    public static array $statusLabels = [
        'valide'       => 'Validé',
        'non_valide'   => 'Non validé',
        'sous_reserve' => 'Sous réserve',
        'optimisation' => 'Optimisation',
    ];

    /**
     * Types de nature d'erreur
     */
    public static array $natures = [
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
    ];

    public function testCase(): BelongsTo
    {
        return $this->belongsTo(TestCase::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestCaseAssignment::class);
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tester_id');
    }
}
