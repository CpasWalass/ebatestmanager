<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase as TestCaseModel;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCaseStatsTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::factory()->create();
        $chef = User::factory()->create();
        $this->project = Project::factory()->create([
            'client_id' => $client->id,
            'created_by' => $chef->id,
        ]);
    }

    private function createTemplate(): TestCaseTemplate
    {
        return TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);
    }

    public function test_stats_for_empty_collection(): void
    {
        $stats = TestCaseModel::statsFor($this->project->testCases()->get());
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0.0, $stats['taux_execution']);
        $this->assertEquals(0.0, $stats['taux_validation']);
    }

    public function test_stats_for_all_pending(): void
    {
        $tpl = $this->createTemplate();
        TestCaseModel::factory()->count(2)->create(['project_id' => $this->project->id, 'template_id' => $tpl->id, 'progress' => 'a_faire']);
        TestCaseModel::factory()->create(['project_id' => $this->project->id, 'template_id' => $tpl->id, 'progress' => 'en_cours']);

        $stats = TestCaseModel::statsFor($this->project->testCases()->get());
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(0, $stats['executed']);
        $this->assertEquals(3, $stats['non_executed']);
        $this->assertEquals(0.0, $stats['taux_execution']);
    }

    public function test_stats_for_executed_with_verdicts(): void
    {
        $tpl = $this->createTemplate();
        TestCaseModel::factory()->count(2)->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'termine', 'verdict' => 'valide',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'termine', 'verdict' => 'non_valide',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'termine', 'verdict' => 'sous_reserve',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'a_faire',
        ]);

        $stats = TestCaseModel::statsFor($this->project->testCases()->get());
        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(4, $stats['executed']);
        $this->assertEquals(1, $stats['non_executed']);
        $this->assertEquals(2, $stats['valide']);
        $this->assertEquals(1, $stats['non_valide']);
        $this->assertEquals(1, $stats['sous_reserve']);
        $this->assertEquals(80.0, $stats['taux_execution']);
        $this->assertEquals(50.0, $stats['taux_validation']);
    }

    public function test_stats_for_blocked_without_verdict(): void
    {
        $tpl = $this->createTemplate();
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'bloque', 'verdict' => null,
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'bloque', 'verdict' => 'valide',
        ]);

        $stats = TestCaseModel::statsFor($this->project->testCases()->get());
        $this->assertEquals(2, $stats['executed']);
        $this->assertEquals(1, $stats['bloque']);
        $this->assertEquals(1, $stats['valide']);
    }

    public function test_stats_for_optimisation_verdict(): void
    {
        $tpl = $this->createTemplate();
        TestCaseModel::factory()->count(2)->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'progress' => 'termine', 'verdict' => 'optimisation',
        ]);

        $stats = TestCaseModel::statsFor($this->project->testCases()->get());
        $this->assertEquals(2, $stats['optimisation']);
        $this->assertEquals(100.0, $stats['taux_execution']);
        $this->assertEquals(0.0, $stats['taux_validation']);
    }

    public function test_client_stats_for_empty(): void
    {
        $stats = TestCaseModel::clientStatsFor($this->project->testCases()->get());
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0.0, $stats['taux_approbation']);
    }

    public function test_client_stats_for_mixed_statuses(): void
    {
        $tpl = $this->createTemplate();
        TestCaseModel::factory()->count(2)->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'client_status' => 'validated',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'client_status' => 'rejected',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id, 'template_id' => $tpl->id,
            'client_status' => 'pending',
        ]);

        $stats = TestCaseModel::clientStatsFor($this->project->testCases()->get());
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['validated']);
        $this->assertEquals(1, $stats['rejected']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(50.0, $stats['taux_approbation']);
    }
}
