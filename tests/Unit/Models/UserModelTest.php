<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
    }

    public function test_email_is_lowercased_on_save(): void
    {
        $user = User::factory()->create(['email' => '  FOO@BAR.COM  ']);
        $this->assertEquals('foo@bar.com', $user->fresh()->email);
    }

    public function test_email_is_lowercased_without_spaces(): void
    {
        $user = User::factory()->create(['email' => 'Test@Example.COM']);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }

    public function test_is_chef_projet(): void
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $this->assertTrue($user->fresh()->isChefProjet());
        $this->assertFalse($user->fresh()->isTesteur());
    }

    public function test_is_testeur(): void
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        $this->assertTrue($user->fresh()->isTesteur());
        $this->assertFalse($user->fresh()->isChefProjet());
    }

    public function test_is_developpeur(): void
    {
        $user = User::factory()->create();
        $user->assignRole('developer');
        $this->assertTrue($user->fresh()->isDeveloppeur());
    }

    public function test_is_client(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $this->assertTrue($user->fresh()->isClient());
    }
}
