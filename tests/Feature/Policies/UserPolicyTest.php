<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('manage users');

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');

        return $user;
    }

    public function test_admin_can_view_any_user(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertTrue($admin->can('viewAny', User::class));
    }

    public function test_tester_cannot_view_any_user(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('viewAny', User::class));
    }

    public function test_admin_can_view_specific_user(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $other = User::factory()->create();
        $this->assertTrue($admin->can('view', $other));
    }

    public function test_user_can_view_self(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertTrue($tester->can('view', $tester));
    }

    public function test_tester_cannot_view_other_user(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $other = User::factory()->create();
        $this->assertFalse($tester->can('view', $other));
    }

    public function test_admin_can_create_user(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertTrue($admin->can('create', User::class));
    }

    public function test_tester_cannot_create_user(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('create', User::class));
    }

    public function test_user_can_update_self(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertTrue($tester->can('update', $tester));
    }

    public function test_admin_can_update_other_user(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $other = User::factory()->create();
        $this->assertTrue($admin->can('update', $other));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_can_delete_other_user(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $other = User::factory()->create();
        $this->assertTrue($admin->can('delete', $other));
    }

    public function test_user_with_assign_tests_can_assign(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('assign tests');
        $this->assertTrue($user->can('assign', User::class));
    }

    public function test_user_without_permission_cannot_assign(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertFalse($tester->can('assign', User::class));
    }
}
