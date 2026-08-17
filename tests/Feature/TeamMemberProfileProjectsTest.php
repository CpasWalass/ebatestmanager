<?php

namespace Tests\Feature;

use App\Livewire\TeamMemberProfile;
use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase as TestCaseModel;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\TestExecution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamMemberProfileProjectsTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(): Client
    {
        return Client::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'tenant_id' => 'eba',
        ]);
    }

    private function createAdmin(): User
    {
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']));
        $admin->givePermissionTo('manage users');

        return $admin;
    }

    public function test_developer_sees_assigned_projects(): void
    {
        $admin = $this->createAdmin();
        $developer = User::factory()->create(['name' => 'Marie Dev']);
        $developer->assignRole(Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']));

        $project = Project::create([
            'name' => 'Projet Alpha',
            'type' => 'iat',
            'status' => 'in_progress',
            'client_id' => $this->createClient()->id,
            'created_by' => $admin->id,
            'tenant_id' => 'eba',
        ]);

        $developer->projectsAsDeveloper()->attach($project);

        $this->actingAs($admin);

        Livewire::test(TeamMemberProfile::class, ['user' => $developer])
            ->assertSee('Projets assignés')
            ->assertSee('Projet Alpha')
            ->assertSee('Voir')
            ->assertDontSee('Tests effectués')
            ->assertDontSee('Projets créés');
    }

    public function test_tester_sees_executions_not_projects(): void
    {
        $admin = $this->createAdmin();
        $tester = User::factory()->create(['name' => 'Jean Testeur']);
        $tester->assignRole(Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']));

        $project = Project::create([
            'name' => 'Projet Beta',
            'type' => 'iat',
            'client_id' => $this->createClient()->id,
            'created_by' => $admin->id,
            'tenant_id' => 'eba',
        ]);

        $template = TestCaseTemplate::create(['name' => 'Template A', 'project_id' => $project->id, 'fields' => [], 'tenant_id' => 'eba']);
        $tc = TestCaseModel::create(['template_id' => $template->id, 'project_id' => $project->id, 'tenant_id' => 'eba', 'data' => []]);
        $assignment = TestCaseAssignment::create([
            'project_id' => $project->id,
            'template_id' => $template->id,
            'user_id' => $tester->id,
            'tenant_id' => 'eba',
            'status' => 'in_progress',
        ]);

        TestExecution::create([
            'test_case_id' => $tc->id,
            'tester_id' => $tester->id,
            'assignment_id' => $assignment->id,
            'status' => 'valide',
            'nature' => 'Erreurs Fonctionnelles',
            'tenant_id' => 'eba',
        ]);

        $this->actingAs($admin);

        Livewire::test(TeamMemberProfile::class, ['user' => $tester])
            ->assertSee('Tests effectués')
            ->assertSee('Erreurs Fonctionnelles')
            ->assertDontSee('Projets assignés')
            ->assertDontSee('Projets créés');
    }

    public function test_chef_project_sees_created_projects_only(): void
    {
        $admin = $this->createAdmin();

        Project::create([
            'name' => 'Projet Gamma',
            'type' => 'iat',
            'client_id' => $this->createClient()->id,
            'created_by' => $admin->id,
            'tenant_id' => 'eba',
        ]);

        $this->actingAs($admin);

        Livewire::test(TeamMemberProfile::class, ['user' => $admin])
            ->assertSee('Projets créés')
            ->assertSee('Projet Gamma')
            ->assertDontSee('Projets assignés')
            ->assertDontSee('Tests effectués');
    }

    public function test_developer_count_in_stats(): void
    {
        $admin = $this->createAdmin();
        $developer = User::factory()->create();
        $developer->assignRole(Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']));

        $client = $this->createClient();
        $p1 = Project::create(['name' => 'P1', 'type' => 'iat', 'client_id' => $client->id, 'created_by' => $admin->id, 'tenant_id' => 'eba']);
        $p2 = Project::create(['name' => 'P2', 'type' => 'uat', 'client_id' => $client->id, 'created_by' => $admin->id, 'tenant_id' => 'eba']);
        $developer->projectsAsDeveloper()->attach([$p1->id, $p2->id]);

        $this->actingAs($admin);

        Livewire::test(TeamMemberProfile::class, ['user' => $developer])
            ->assertSee('Projets assignés')
            ->assertSee('2');
    }
}
