<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AssignTesters;
use App\Models\Client;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AssignTestersTest extends TestCase
{
    use RefreshDatabase;

    private function createRolesAndPermissions(): void
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign tests', 'guard_name' => 'web']);
    }

    private function chef(): User
    {
        $user = User::factory()->create();
        $user->assignRole('chef_project');
        $user->givePermissionTo('assign tests');

        return $user;
    }

    private function tester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');

        return $user;
    }

    public function test_assign_tester_to_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => $client->id]);
        $this->actingAs($chef);

        Livewire::test(AssignTesters::class)
            ->call('openModal', $project->id, null)
            ->set("selectedTesters.{$tester->id}", true)
            ->call('save');

        $this->assertDatabaseHas('test_case_assignments', [
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_unassign_tester_from_project(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => $client->id]);

        TestCaseAssignment::factory()->create([
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($chef);

        Livewire::test(AssignTesters::class)
            ->call('openModal', $project->id, null)
            ->set('selectedTesters', [])
            ->call('save');

        $this->assertSoftDeleted('test_case_assignments', [
            'user_id' => $tester->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_assign_sends_notification_message(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => $client->id]);
        $this->actingAs($chef);

        Livewire::test(AssignTesters::class)
            ->call('openModal', $project->id, null)
            ->set("selectedTesters.{$tester->id}", true)
            ->call('save');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $chef->id,
            'receiver_id' => $tester->id,
            'type' => 'system',
        ]);
    }

    public function test_assign_to_template(): void
    {
        $this->createRolesAndPermissions();
        $chef = $this->chef();
        $tester = $this->tester();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['created_by' => $chef->id, 'client_id' => $client->id]);
        $template = TestCaseTemplate::factory()->create(['project_id' => $project->id]);
        $this->actingAs($chef);

        Livewire::test(AssignTesters::class)
            ->call('openModal', null, $template->id)
            ->set("selectedTesters.{$tester->id}", true)
            ->call('save');

        $this->assertDatabaseHas('test_case_assignments', [
            'user_id' => $tester->id,
            'template_id' => $template->id,
        ]);
    }
}
