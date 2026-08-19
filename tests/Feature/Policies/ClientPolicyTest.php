<?php

namespace Tests\Feature\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage clients', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view reports', 'guard_name' => 'web']);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('manage clients');

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        $user->givePermissionTo('view reports');

        return $user;
    }

    public function test_admin_can_view_any_client(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertTrue($admin->can('viewAny', Client::class));
    }

    public function test_tester_with_view_reports_can_view_any_client(): void
    {
        $this->createRolesAndPermissions();
        $tester = $this->tester();
        $this->assertTrue($tester->can('viewAny', Client::class));
    }

    public function test_admin_can_create_client(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertTrue($admin->can('create', Client::class));
    }

    public function test_tester_cannot_create_client(): void
    {
        $this->createRolesAndPermissions();
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $this->assertFalse($tester->can('create', Client::class));
    }

    public function test_admin_can_update_client(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $client = Client::factory()->create();
        $this->assertTrue($admin->can('update', $client));
    }

    public function test_admin_can_delete_client(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $client = Client::factory()->create();
        $this->assertTrue($admin->can('delete', $client));
    }

    public function test_admin_can_restore_client(): void
    {
        $this->createRolesAndPermissions();
        $admin = $this->admin();
        $this->assertTrue($admin->can('restore', new Client));
    }
}
