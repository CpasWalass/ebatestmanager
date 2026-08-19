<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectManager;
use App\Models\Client;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $chef;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage testcases', 'guard_name' => 'web']);

        $this->chef = User::factory()->create();
        $this->chef->assignRole('chef_project');
        $this->chef->givePermissionTo(['manage projects', 'manage testcases']);
    }

    public function test_chef_can_see_projects(): void
    {
        $this->actingAs($this->chef);

        Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => Client::factory()->create()->id, 'name' => 'Mon Projet']);

        Livewire::test(ProjectManager::class)
            ->assertSee('Mon Projet');
    }

    public function test_chef_can_create_project(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($this->chef);

        Livewire::test(ProjectManager::class)
            ->set('name', 'Nouveau Projet')
            ->set('description', 'Description test')
            ->set('type', 'iat')
            ->set('client_id', $client->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', ['name' => 'Nouveau Projet', 'created_by' => $this->chef->id]);
    }

    public function test_chef_can_update_project(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => $client->id]);
        $this->actingAs($this->chef);

        Livewire::test(ProjectManager::class)
            ->call('editProject', $project->id)
            ->set('name', 'Projet Modifie')
            ->set('description', 'Nouvelle description')
            ->set('type', 'iat')
            ->set('client_id', $client->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Projet Modifie']);
    }

    public function test_chef_can_delete_project(): void
    {
        $project = Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => Client::factory()->create()->id]);
        $this->actingAs($this->chef);

        Livewire::test(ProjectManager::class)
            ->call('deleteProject', $project->id);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_search_filters_projects(): void
    {
        $this->actingAs($this->chef);

        Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => Client::factory()->create()->id, 'name' => 'Alpha']);
        Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => Client::factory()->create()->id, 'name' => 'Beta']);

        Livewire::test(ProjectManager::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha')
            ->assertDontSee('Beta');
    }

    public function test_tester_sees_only_assigned_projects(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $client = Client::factory()->create();

        $p1 = Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => $client->id, 'name' => 'Assigned']);
        $p2 = Project::factory()->create(['created_by' => $this->chef->id, 'client_id' => $client->id, 'name' => 'NotAssigned']);

        TestCaseAssignment::factory()->create(['user_id' => $tester->id, 'project_id' => $p1->id]);

        $this->actingAs($tester);

        Livewire::test(ProjectManager::class)
            ->assertSee('Assigned')
            ->assertDontSee('NotAssigned');
    }
}
