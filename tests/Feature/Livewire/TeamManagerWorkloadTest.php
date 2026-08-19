<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TeamManager;
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

class TeamManagerWorkloadTest extends TestCase
{
    use RefreshDatabase;

    protected User $chef;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);

        $this->chef = User::factory()->create();
        $this->chef->assignRole('chef_project');
        $this->chef->givePermissionTo('manage users');
    }

    public function test_tester_shows_workload_count(): void
    {
        $tester = User::factory()->create(['name' => 'Jean Testeur']);
        $tester->assignRole('tester');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id, 'created_by' => $this->chef->id]);
        $tpl = TestCaseTemplate::factory()->create(['project_id' => $project->id]);

        TestCaseModel::factory()->count(3)->create([
            'project_id' => $project->id,
            'template_id' => $tpl->id,
            'progress' => 'a_faire',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('Jean Testeur')
            ->assertSee('3 test(s)');
    }

    public function test_tester_shows_executed_count(): void
    {
        $tester = User::factory()->create(['name' => 'Jean Testeur']);
        $tester->assignRole('tester');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id, 'created_by' => $this->chef->id]);
        $tpl = TestCaseTemplate::factory()->create(['project_id' => $project->id]);

        TestCaseModel::factory()->create([
            'project_id' => $project->id,
            'template_id' => $tpl->id,
            'progress' => 'termine',
        ]);
        TestCaseModel::factory()->create([
            'project_id' => $project->id,
            'template_id' => $tpl->id,
            'progress' => 'a_faire',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('50%');
    }

    public function test_non_tester_shows_zero_workload(): void
    {
        $dev = User::factory()->create(['name' => 'Marie Dev']);
        $dev->assignRole('developer');

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('Marie Dev')
            ->assertDontSee('0 test(s)');
    }

    public function test_chef_project_shows_zero_workload(): void
    {
        $otherChef = User::factory()->create(['name' => 'Autre Chef']);
        $otherChef->assignRole('chef_project');

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('Autre Chef')
            ->assertDontSee('0 test(s)');
    }

    public function test_client_role_shows_zero_workload(): void
    {
        $clientUser = User::factory()->create(['name' => 'Client User']);
        $clientUser->assignRole('client');

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('Client User')
            ->assertDontSee('0 test(s)');
    }

    public function test_tester_without_assignments_shows_zero_workload(): void
    {
        $tester = User::factory()->create(['name' => ' tester sans assignment']);
        $tester->assignRole('tester');

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('tester sans assignment')
            ->assertSee('0 test(s)');
    }

    public function test_archived_project_excluded_from_workload(): void
    {
        $tester = User::factory()->create(['name' => 'Jean Testeur']);
        $tester->assignRole('tester');

        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'created_by' => $this->chef->id,
            'status' => 'archived',
        ]);
        $tpl = TestCaseTemplate::factory()->create(['project_id' => $project->id]);

        TestCaseModel::factory()->count(5)->create([
            'project_id' => $project->id,
            'template_id' => $tpl->id,
            'progress' => 'termine',
        ]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('0 test(s)');
    }

    public function test_role_filter_only_shows_testers(): void
    {
        $tester = User::factory()->create(['name' => 'Jean Testeur']);
        $tester->assignRole('tester');

        $dev = User::factory()->create(['name' => 'Marie Dev']);
        $dev->assignRole('developer');

        $this->actingAs($this->chef);

        Livewire::test(TeamManager::class)
            ->assertSee('Jean Testeur')
            ->assertSee('Marie Dev')
            ->set('roleFilter', 'tester')
            ->assertSee('Jean Testeur')
            ->assertDontSee('Marie Dev');
    }
}
