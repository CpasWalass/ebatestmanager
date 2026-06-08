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
    ];

    protected $casts = [
        'fields' => 'array',
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
     * Get default test case fields structure
     */
    public static function defaultFields()
    {
        return [
            ['name' => 'cas_test', 'label' => 'CAS DE TEST', 'type' => 'text', 'required' => true],
            ['name' => 'etat_test', 'label' => 'ETAT DE TEST', 'type' => 'select', 'required' => true, 'options' => ['À faire', 'En cours', 'Bloqué', 'Terminé']],
            ['name' => 'modules', 'label' => 'MODULES', 'type' => 'text', 'required' => false],
            ['name' => 'fonctionnalites', 'label' => 'FONCTIONNALITES', 'type' => 'text', 'required' => false],
            ['name' => 'scenarios_test', 'label' => 'SCENARIOS DE TEST', 'type' => 'textarea', 'required' => true],
            ['name' => 'resultats_attendus', 'label' => 'RESULTATS ATTENDUS', 'type' => 'textarea', 'required' => true],
            ['name' => 'resultats_obtenus', 'label' => 'RESULTATS OBTENUS', 'type' => 'textarea', 'required' => false],
            ['name' => 'nature', 'label' => 'NATURE', 'type' => 'select', 'required' => false, 'options' => [
                'Erreurs Fonctionnelles',
                'Erreurs de Validation / Saisie',
                'Erreurs d\'Interface (UI/UX)',
                'Erreurs Techniques',
                'Erreurs de Performance',
                'Erreurs de Sécurité',
                'Erreurs de Données',
                'Erreurs d\'Intégration',
                'Erreurs de Compatibilité',
                'Erreurs de Workflow / Navigation',
            ]],
            ['name' => 'status', 'label' => 'STATUS', 'type' => 'select', 'required' => false, 'options' => ['Validé', 'Non validé', 'Sous réserve', 'Optimisation']],
            ['name' => 'commentaires', 'label' => 'COMMENTAIRES', 'type' => 'textarea', 'required' => false],
        ];
    }
}
