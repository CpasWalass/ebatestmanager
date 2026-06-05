<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Project;
use App\Models\TestCase;

class CreateUatTestCase extends Component
{
    public $projectId = '';
    public $titre = '';
    public $scenario = '';
    public $resultat = '';
    
    public $showSuccessMessage = false;

    protected $rules = [
        'projectId' => 'required|exists:projects,id',
        'titre'     => 'required|string|max:255',
        'scenario'  => 'required|string|min:10',
        'resultat'  => 'required|string|min:10',
    ];

    protected $messages = [
        'projectId.required' => 'Veuillez sélectionner un projet.',
        'titre.required'     => 'Le titre est obligatoire.',
        'scenario.required'  => 'Veuillez décrire le scénario attendu.',
        'resultat.required'  => 'Veuillez décrire le résultat métier attendu.',
    ];

    public function submit()
    {
        $this->validate();

        $tenantId = tenant('id') ?? auth()->user()->tenant_id;

        TestCase::create([
            'project_id'  => $this->projectId,
            'template_id' => null, // Ou un template UAT par défaut si configuré
            'data'        => [
                'cas_test'           => $this->titre,
                'scenarios_test'     => $this->scenario,
                'resultats_attendus' => $this->resultat,
                // On laisse les champs techniques vides car créés par le client
                'modules'            => 'UAT Client',
                'fonctionnalites'    => 'Non spécifié',
            ],
            'type'        => 'uat',
            'tenant_id'   => $tenantId,
        ]);

        $this->reset(['titre', 'scenario', 'resultat']);
        $this->showSuccessMessage = true;
        
        // Hide success message after 3 seconds
        $this->dispatch('uat-created');
    }

    public function render()
    {
        $tenantId = tenant('id') ?? auth()->user()->tenant_id;
        $projets = Project::where('tenant_id', $tenantId)->get();

        return view('livewire.client.create-uat-test-case', [
            'projets' => $projets
        ]);
    }
}
