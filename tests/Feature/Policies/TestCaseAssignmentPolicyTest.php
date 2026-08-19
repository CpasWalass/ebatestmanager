<?php

namespace Tests\Feature\Policies;

use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TestCaseAssignmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
    }

    public function test_user_with_assign_permission_can_create(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('assign tests');
        $this->assertTrue($user->can('create', TestCaseAssignment::class));
    }

    public function test_user_without_permission_cannot_create(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $this->assertFalse($user->can('create', TestCaseAssignment::class));
    }

    public function test_admin_can_delete_assignment(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('assign tests');
        $assignment = TestCaseAssignment::factory()->create();
        $this->assertTrue($user->can('delete', $assignment));
    }

    public function test_user_can_delete_own_assignment(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $assignment = TestCaseAssignment::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->can('delete', $assignment));
    }

    public function test_user_cannot_delete_others_assignment_without_permission(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $assignment = TestCaseAssignment::factory()->create();
        $this->assertFalse($user->can('delete', $assignment));
    }
}
