<?php

namespace Tests\Feature\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage testcases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
    }

    private function chef(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('manage projects');

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');

        return $user;
    }

    private function developer(): User
    {
        $user = User::factory()->create();
        $user->assignRole('developer');

        return $user;
    }

    public function test_chef_can_view_any_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('viewAny', Project::class));
    }

    public function test_tester_cannot_view_any_project(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('viewAny', Project::class));
    }

    public function test_chef_can_view_project_he_created(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);

        $this->assertTrue($chef->can('view', $project));
    }

    public function test_assigned_tester_can_view_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $tester->givePermissionTo('manage testcases');
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);

        $this->assertTrue($tester->can('view', $project));
    }

    public function test_unassigned_tester_cannot_view_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $tester->givePermissionTo('manage testcases');
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);

        $this->assertFalse($tester->can('view', $project));
    }

    public function test_developer_on_project_can_view_it(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $dev = User::factory()->create();
        $dev->assignRole('developer');
        $dev->givePermissionTo('manage testcases');
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);
        $project->developers()->attach($dev);

        $this->assertTrue($dev->can('view', $project));
    }

    public function test_chef_can_create_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('create', Project::class));
    }

    public function test_tester_cannot_create_project(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('create', Project::class));
    }

    public function test_creator_can_update_own_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);

        $this->assertTrue($chef->can('update', $project));
    }

    public function test_chef_cannot_update_other_chefs_project(): void
    {
        $this->createRolesAndPermissions();
        $chef1 = $this->chef();
        $chef2 = $this->chef();
        $project = Project::factory()->create(['created_by' => $chef2->id, 'client_id' => Client::factory()->create()->id]);

        $this->assertFalse($chef1->can('update', $project));
    }

    public function test_creator_can_delete_own_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);

        $this->assertTrue($chef->can('delete', $project));
    }

    public function test_any_admin_can_restore_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('restore', new Project));
    }
}
