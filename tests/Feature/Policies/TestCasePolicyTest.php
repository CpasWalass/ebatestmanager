<?php

namespace Tests\Feature\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase as TestCaseModel;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TestCasePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage testcases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'validate testcases', 'guard_name' => 'web']);
    }

    private function chef(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo(['manage testcases', 'manage projects']);

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('manage testcases');

        return $user;
    }

    private function client(): User
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $user->givePermissionTo('validate testcases');

        return $user;
    }

    private function createTestCase(?Project $project = null): TestCaseModel
    {
        $project ??= Project::factory()->create(['client_id' => Client::factory()->create()->id, 'created_by' => $this->chef()->id]);
        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);

        return TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id]);
    }

    public function test_chef_can_view_any_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('viewAny', TestCaseModel::class));
    }

    public function test_chef_can_view_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tc = $this->createTestCase();
        $this->assertTrue($chef->can('view', $tc));
    }

    public function test_chef_can_create_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('create', TestCaseModel::class));
    }

    public function test_chef_can_delete_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tc = $this->createTestCase();
        $this->assertTrue($chef->can('delete', $tc));
    }

    public function test_chef_can_update_any_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tc = $this->createTestCase();
        $this->assertTrue($chef->can('update', $tc));
    }

    public function test_assigned_tester_can_update_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();

        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);
        TestCaseAssignment::factory()->create(['user_id' => $tester->id, 'project_id' => $project->id]);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        $tc = TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id]);

        $this->assertTrue($tester->can('update', $tc));
    }

    public function test_unassigned_tester_cannot_update_test_case(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);
        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        $tc = TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id]);

        $this->assertFalse($tester->can('update', $tc));
    }

    public function test_tester_cannot_create_test_case(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('create', TestCaseModel::class));
    }

    public function test_tester_cannot_delete_test_case(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $tc = $this->createTestCase();
        $this->assertFalse($tester->can('delete', $tc));
    }

    public function test_client_can_validate_uat_test_case_when_assigned(): void
    {
        $this->createRolesAndPermissions();
        $client = $this->client();
        $chef = $this->chef();

        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id, 'type' => 'uat']);
        TestCaseAssignment::factory()->create(['user_id' => $client->id, 'project_id' => $project->id]);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        $tc = TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id, 'type' => 'uat']);

        $this->assertTrue($client->can('validate', $tc));
    }

    public function test_client_cannot_validate_iat_test_case(): void
    {
        $this->createRolesAndPermissions();
        $client = $this->client();
        $chef = $this->chef();

        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => Client::factory()->create()->id]);
        TestCaseAssignment::factory()->create(['user_id' => $client->id, 'project_id' => $project->id]);

        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        $tc = TestCaseModel::factory()->create(['project_id' => $project->id, 'template_id' => $template->id]);

        $this->assertFalse($client->can('validate', $tc));
    }

    public function test_client_cannot_update_test_case(): void
    {
        $this->createRolesAndPermissions();
        $client = $this->client();
        $tc = $this->createTestCase();
        $this->assertFalse($client->can('update', $tc));
    }
}
