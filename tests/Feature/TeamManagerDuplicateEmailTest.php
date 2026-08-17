<?php

namespace Tests\Feature;

use App\Livewire\TeamManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamManagerDuplicateEmailTest extends TestCase
{
    use RefreshDatabase;

    private function chefAdmin(): User
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('chef_project');
        $admin->givePermissionTo('manage users');

        $this->actingAs($admin);

        return $admin;
    }

    public function test_exact_duplicate_email_is_rejected_with_clear_message(): void
    {
        Mail::fake();
        $this->chefAdmin();

        User::factory()->create(['email' => 'alice@example.com']);

        Livewire::test(TeamManager::class)
            ->set('name', 'Bob Example')
            ->set('email', 'alice@example.com')
            ->set('role', 'tester')
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);

        $this->assertDatabaseCount('users', 2);
    }

    public function test_duplicate_email_with_different_case_is_rejected(): void
    {
        Mail::fake();
        $this->chefAdmin();

        User::factory()->create(['email' => 'alice@example.com']);

        Livewire::test(TeamManager::class)
            ->set('name', 'Bob Example')
            ->set('email', 'ALICE@Example.com')
            ->set('role', 'tester')
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);

        $this->assertDatabaseCount('users', 2);
    }

    public function test_email_is_stored_lowercased(): void
    {
        Mail::fake();
        $this->chefAdmin();

        Livewire::test(TeamManager::class)
            ->set('name', 'Alice Example')
            ->set('email', '  ALICE@EXAMPLE.COM  ')
            ->set('role', 'tester')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'ALICE@EXAMPLE.COM']);
    }

    public function test_unique_email_allows_creation(): void
    {
        Mail::fake();
        $this->chefAdmin();

        Livewire::test(TeamManager::class)
            ->set('name', 'Alice Example')
            ->set('email', 'alice@example.com')
            ->set('role', 'tester')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    public function test_success_message_is_displayed_after_creation(): void
    {
        Mail::fake();
        $this->chefAdmin();

        Livewire::test(TeamManager::class)
            ->set('name', 'Alice Example')
            ->set('email', 'alice@example.com')
            ->set('role', 'tester')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Utilisateur créé avec succès');
    }
}
