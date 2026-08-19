<?php

namespace Tests\Feature\Policies;

use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TestCaseTemplatePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage testcases', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage projects', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
    }

    private function chef(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('manage testcases');

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('manage testcases');

        return $user;
    }

    public function test_chef_can_view_any_template(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('viewAny', TestCaseTemplate::class));
    }

    public function test_chef_can_create_template(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $this->assertTrue($chef->can('create', TestCaseTemplate::class));
    }

    public function test_chef_can_update_template(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $template = TestCaseTemplate::factory()->create();
        $this->assertTrue($chef->can('update', $template));
    }

    public function test_chef_can_delete_template(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $template = TestCaseTemplate::factory()->create();
        $this->assertTrue($chef->can('delete', $template));
    }

    public function test_tester_cannot_create_template(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('create', TestCaseTemplate::class));
    }

    public function test_assigned_tester_can_view_template(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $template = TestCaseTemplate::factory()->create();

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'template_id' => $template->id,
        ]);

        $this->assertTrue($tester->can('view', $template));
    }

    public function test_tester_with_project_assignment_can_view_template(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $template = TestCaseTemplate::factory()->create();

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $template->project_id,
        ]);

        $this->assertTrue($tester->can('view', $template));
    }

    public function test_unassigned_tester_cannot_view_template(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $template = TestCaseTemplate::factory()->create();

        $this->assertFalse($tester->can('view', $template));
    }
}
