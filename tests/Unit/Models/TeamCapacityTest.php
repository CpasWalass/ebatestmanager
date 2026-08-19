<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase as TestCaseModel;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\TestExecution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamCapacityTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);

        $client = Client::factory()->create();
        $chef = User::factory()->create();
        $this->project = Project::factory()->create([
            'client_id' => $client->id,
            'created_by' => $chef->id,
        ]);
    }

    private function computeCapacity(User $tester): array
    {
        $activeProjectIds = Project::whereNotIn('status', ['completed', 'archived'])
            ->pluck('id')
            ->toArray();

        $assignedProjectIds = TestCaseAssignment::where('user_id', $tester->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();

        $assignedTemplateIds = TestCaseAssignment::where('user_id', $tester->id)
            ->whereNotNull('template_id')
            ->pluck('template_id')
            ->toArray();

        $assignedCaseIds = TestCaseModel::whereIn('project_id', $activeProjectIds)
            ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                    ->orWhereIn('template_id', $assignedTemplateIds);
            })
            ->pluck('id');

        $total = $assignedCaseIds->count();

        $done = TestCaseModel::whereIn('id', $assignedCaseIds)
            ->whereIn('progress', ['termine', 'bloque'])
            ->count();

        return ['total' => $total, 'done' => $done];
    }

    public function test_counts_progress_termine_as_done(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $tpl = TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);

        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'termine',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'a_faire',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $this->project->id,
        ]);

        $capacity = $this->computeCapacity($tester);

        $this->assertEquals(2, $capacity['total']);
        $this->assertEquals(1, $capacity['done']);
    }

    public function test_counts_progress_bloque_as_done(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $tpl = TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);

        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'bloque',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'en_cours',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $this->project->id,
        ]);

        $capacity = $this->computeCapacity($tester);

        $this->assertEquals(2, $capacity['total']);
        $this->assertEquals(1, $capacity['done']);
    }

    public function test_ignores_stale_test_execution_when_progress_is_a_faire(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $tpl = TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);

        $tc = TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'a_faire',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $this->project->id,
        ]);

        TestExecution::factory()->create([
            'tester_id' => $tester->id,
            'test_case_id' => $tc->id,
        ]);

        $capacity = $this->computeCapacity($tester);

        $this->assertEquals(1, $capacity['total']);
        $this->assertEquals(0, $capacity['done']);
    }

    public function test_counts_progress_done_even_without_test_execution_record(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $tpl = TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);

        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'termine',
            'verdict' => 'valide',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $this->project->id,
        ]);

        $capacity = $this->computeCapacity($tester);

        $this->assertEquals(1, $capacity['total']);
        $this->assertEquals(1, $capacity['done']);
    }

    public function test_counts_done_only_within_active_projects(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $tpl = TestCaseTemplate::factory()->create(['project_id' => $this->project->id]);

        TestCaseModel::factory()->create([
            'project_id' => $this->project->id,
            'template_id' => $tpl->id,
            'progress' => 'termine',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $this->project->id,
        ]);

        $archivedProject = Project::factory()->create([
            'status' => 'archived',
        ]);
        $tplArchived = TestCaseTemplate::factory()->create(['project_id' => $archivedProject->id]);
        TestCaseModel::factory()->create([
            'project_id' => $archivedProject->id,
            'template_id' => $tplArchived->id,
            'progress' => 'termine',
        ]);
        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $archivedProject->id,
        ]);

        $capacity = $this->computeCapacity($tester);

        $this->assertEquals(1, $capacity['total']);
        $this->assertEquals(1, $capacity['done']);
    }
}
