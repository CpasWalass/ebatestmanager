<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\TestCase;
use App\Models\User;
use Illuminate\Console\Command;

class VisualMockCommand extends Command
{
    protected $signature = 'mock:visual {--remove : Supprimer toutes les données de mock}';

    protected $description = 'Génère ou nettoie des données factices pour les tests visuels du tableau de bord';

    public function handle()
    {
        if ($this->option('remove')) {
            $this->removeMockData();
        } else {
            $this->generateMockData();
        }
    }

    private function generateMockData()
    {
        $this->info('Début de la génération des données MOCK...');

        // 1. Trouver un tenant valide ou prendre le premier (eba_togo par défaut)
        $tenant = Tenant::first();
        if (! $tenant) {
            $this->error('Aucun tenant trouvé. Veuillez exécuter le TenantSeeder en premier.');

            return;
        }

        $client = Client::where('tenant_id', $tenant->id)->first() ?? Client::factory()->create(['tenant_id' => $tenant->id]);
        $creator = User::where('tenant_id', $tenant->id)->first() ?? User::factory()->create(['tenant_id' => $tenant->id]);

        // Projet 1 : Clôturé
        $project1 = Project::create([
            'name' => '[MOCK] Refonte E-commerce',
            'description' => 'Test visuel d\'un projet terminé.',
            'status' => 'completed',
            'type' => 'IAT',
            'client_id' => $client->id,
            'created_by' => $creator->id,
            'tenant_id' => $tenant->id,
        ]);
        $this->createTestCases($project1, 12, ['status' => 'validé', 'client_status' => 'validated']);

        // Projet 2 : En cours (IAT)
        $project2 = Project::create([
            'name' => '[MOCK] Application Mobile',
            'description' => 'Test visuel d\'un projet en cours.',
            'status' => 'in_progress',
            'type' => 'IAT',
            'client_id' => $client->id,
            'created_by' => $creator->id,
            'tenant_id' => $tenant->id,
        ]);
        // 5 validés, 2 non validés, 1 sous réserve, 4 non exécutés
        $this->createTestCases($project2, 5, ['status' => 'validé']);
        $this->createTestCases($project2, 2, ['status' => 'échec']);
        $this->createTestCases($project2, 1, ['status' => 'sous réserve']);
        $this->createTestCases($project2, 4, ['status' => '']); // non exécutés

        // Projet 3 : En phase UAT
        $project3 = Project::create([
            'name' => '[MOCK] Portail RH API',
            'description' => 'Test visuel d\'un projet en phase UAT.',
            'status' => 'in_review',
            'type' => 'UAT',
            'client_id' => $client->id,
            'created_by' => $creator->id,
            'tenant_id' => $tenant->id,
        ]);
        // Tous validés IAT, mais côté UAT : 3 approuvés, 2 rejetés, 5 en attente
        $this->createTestCases($project3, 3, ['status' => 'validé', 'client_status' => 'validated']);
        $this->createTestCases($project3, 2, ['status' => 'validé', 'client_status' => 'rejected']);
        $this->createTestCases($project3, 5, ['status' => 'validé', 'client_status' => 'pending']);

        $this->info('Données MOCK générées avec succès ! Vous pouvez recharger votre tableau de bord.');
    }

    private function removeMockData()
    {
        $this->info('Nettoyage des données MOCK...');

        $projects = Project::where('name', 'LIKE', '[MOCK]%')->get();

        $count = 0;
        foreach ($projects as $project) {
            TestCase::where('project_id', $project->id)->forceDelete();
            // Supprimer potentiellement d'autres liaisons (TestCaseAssignment, etc.) si besoin
            $project->forceDelete();
            $count++;
        }

        $this->info("Nettoyage terminé. $count projet(s) MOCK ont été supprimés.");
    }

    private function createTestCases($project, $count, $attributes)
    {
        for ($i = 0; $i < $count; $i++) {
            TestCase::create([
                'project_id' => $project->id,
                'tenant_id' => $project->tenant_id,
                'type' => $project->status === 'in_review' ? 'uat' : 'iat',
                'client_status' => $attributes['client_status'] ?? 'pending',
                'data' => [
                    'titre_cas_test' => 'Mock Test '.uniqid(),
                    'description' => 'Généré automatiquement.',
                    'etat_test' => $attributes['status'] ?? '',
                ],
            ]);
        }
    }
}
