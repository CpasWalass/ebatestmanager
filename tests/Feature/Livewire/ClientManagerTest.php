<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ClientManager;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage clients', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('manage clients');

        return $user;
    }

    public function test_admin_can_see_clients(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Client::factory()->create(['name' => 'Client Alpha']);

        Livewire::test(ClientManager::class)
            ->assertSee('Client Alpha');
    }

    public function test_admin_can_create_client(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Livewire::test(ClientManager::class)
            ->set('name', 'Nouveau Client')
            ->set('email', 'nouveau@client.com')
            ->set('phone', '+225 01 02 03 04')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', ['email' => 'nouveau@client.com']);
    }

    public function test_create_client_requires_email(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Livewire::test(ClientManager::class)
            ->set('name', 'Client Sans Email')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Client::factory()->create(['email' => 'existing@test.com']);

        Livewire::test(ClientManager::class)
            ->set('name', 'Duplicate Client')
            ->set('email', 'existing@test.com')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_admin_can_delete_client_without_projects(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $client = Client::factory()->create();

        Livewire::test(ClientManager::class)
            ->call('deleteClient', $client->id);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_cannot_delete_client_with_projects(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $client = Client::factory()->create();
        Project::factory()->create(['client_id' => $client->id, 'created_by' => $admin->id]);

        Livewire::test(ClientManager::class)
            ->call('deleteClient', $client->id);

        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_search_filters_clients(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Client::factory()->create(['name' => 'Alpha Corp']);
        Client::factory()->create(['name' => 'Beta Inc']);

        Livewire::test(ClientManager::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha Corp')
            ->assertDontSee('Beta Inc');
    }
}
