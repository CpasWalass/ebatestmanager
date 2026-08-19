<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
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
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);
    }

    public function test_tester_redirects_to_testeur_dashboard(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'must_change_password' => false,
        ]);
        $user->assignRole('tester');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response = $this->get('/testeur/dashboard');
        $response->assertOk();
    }

    public function test_developer_redirects_to_developpeur_dashboard(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'must_change_password' => false,
        ]);
        $user->assignRole('developer');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response = $this->get('/developpeur/dashboard');
        $response->assertOk();
    }

    public function test_client_redirects_to_client_dashboard(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'must_change_password' => false,
        ]);
        $user->assignRole('client');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response = $this->get('/client/dashboard');
        $response->assertOk();
    }

    public function test_locked_account_cannot_login(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'locked_until' => now()->addMinutes(30),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_login(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_password_change_required_redirects_to_profile(): void
    {
        $this->createRolesAndPermissions();
        $user = User::factory()->create([
            'must_change_password' => true,
            'temporary_password_hash' => bcrypt('temp123'),
        ]);
        $user->assignRole('tester');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'temp123',
        ]);

        $this->assertAuthenticated();
        $this->get('/profile')->assertOk();
    }
}
