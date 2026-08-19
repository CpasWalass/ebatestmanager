<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TestCaseManager;
use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase as TestCaseModel;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TestCaseManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $chef;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage testcases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);

        $this->chef = User::factory()->create();
        $this->chef->assignRole('chef_project');
        $this->chef->givePermissionTo(['manage testcases', 'manage projects']);
    }

    private function createProject(array $overrides = []): Project
    {
        $client = Client::factory()->create();

        return Project::factory()->create(array_merge([
            'created_by' => $this->chef->id,
            'client_id' => $client->id,
        ], $overrides));
    }

    public function test_chef_can_see_templates(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        TestCaseTemplate::factory()->create(['project_id' => $project->id, 'name' => 'Mon Template']);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->assertSee('Mon Template');
    }

    public function test_chef_can_create_template(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->set('name', 'Nouveau Template')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('test_case_templates', [
            'name' => 'Nouveau Template',
            'project_id' => $project->id,
        ]);
    }

    public function test_template_name_must_be_at_least_3_chars(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->set('name', 'AB')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_delete_template_cascades_to_test_cases(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        TestCaseModel::factory()->count(3)->create([
            'project_id' => $project->id,
            'template_id' => $template->id,
        ]);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('deleteTemplate', $template->id);

        $this->assertDatabaseMissing('test_case_templates', ['id' => $template->id]);
        $this->assertDatabaseCount('test_cases', 0);
    }

    public function test_delete_template_without_test_cases(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('deleteTemplate', $template->id);

        $this->assertDatabaseMissing('test_case_templates', ['id' => $template->id]);
    }

    public function test_promote_to_uat(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id, 'type' => 'iat']);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('promoteToUat');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'type' => 'uat']);
        $this->assertDatabaseHas('test_cases', ['project_id' => $project->id, 'type' => 'uat']);
    }

    public function test_revert_to_iat(): void
    {
        $project = $this->createProject(['type' => 'uat', 'status' => 'in_review']);
        $this->actingAs($this->chef);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        TestCaseModel::factory()->create([
            'project_id' => $project->id, 'template_id' => $template->id,
            'type' => 'uat', 'client_status' => 'validated',
        ]);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('revertToIat');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'type' => 'iat', 'status' => 'in_progress']);
    }

    public function test_close_project(): void
    {
        $project = $this->createProject();
        $this->actingAs($this->chef);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('closeProject');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'completed']);
    }

    public function test_reopen_project(): void
    {
        $project = $this->createProject(['status' => 'completed']);
        $this->actingAs($this->chef);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('reopenProject');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'in_progress']);
    }

    public function test_non_creator_cannot_reopen_project(): void
    {
        $project = $this->createProject(['status' => 'completed']);

        $otherChef = User::factory()->create();
        $otherChef->assignRole('chef_project');
        $otherChef->givePermissionTo(['manage testcases', 'manage projects']);

        $this->actingAs($otherChef);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->call('reopenProject')
            ->assertStatus(403);
    }

    public function test_tester_sees_assigned_templates(): void
    {
        $project = $this->createProject();

        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $tester->givePermissionTo('manage testcases');

        $t1 = TestCaseTemplate::factory()->create(['project_id' => $project->id, 'name' => 'Assigned Template']);
        $t2 = TestCaseTemplate::factory()->create(['project_id' => $project->id, 'name' => 'Unassigned Template']);

        TestCaseAssignment::factory()->create(['user_id' => $tester->id, 'project_id' => $project->id]);

        $this->actingAs($tester);

        Livewire::test(TestCaseManager::class, ['project' => $project])
            ->assertSee('Assigned Template')
            ->assertSee('Unassigned Template');
    }
}
