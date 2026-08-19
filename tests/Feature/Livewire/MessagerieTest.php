<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Messagerie;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MessagerieTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        Role::firstOrCreate(['name' => 'chef_project', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('chef_project');

        return $user;
    }

    public function test_can_send_message(): void
    {
        $sender = $this->createUser();
        $receiver = User::factory()->create(['tenant_id' => 'eba']);
        $this->actingAs($sender);

        Livewire::test(Messagerie::class)
            ->call('selectUser', $receiver->id)
            ->set('newMessage', 'Bonjour!')
            ->call('sendMessage');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Bonjour!',
        ]);
    }

    public function test_empty_message_is_rejected(): void
    {
        $sender = $this->createUser();
        $receiver = User::factory()->create(['tenant_id' => 'eba']);
        $this->actingAs($sender);

        Livewire::test(Messagerie::class)
            ->call('selectUser', $receiver->id)
            ->set('newMessage', '')
            ->call('sendMessage')
            ->assertHasErrors(['newMessage']);
    }

    public function test_conversation_shows_messages_between_users(): void
    {
        $alice = $this->createUser();
        $bob = User::factory()->create(['tenant_id' => 'eba']);

        Message::factory()->create([
            'sender_id' => $alice->id,
            'receiver_id' => $bob->id,
            'content' => 'Salut Bob!',
        ]);
        Message::factory()->create([
            'sender_id' => $bob->id,
            'receiver_id' => $alice->id,
            'content' => 'Salut Alice!',
        ]);

        $this->actingAs($alice);

        Livewire::test(Messagerie::class)
            ->call('selectUser', $bob->id);

        $conversation = Message::conversation($alice->id, $bob->id)->get();
        $this->assertCount(2, $conversation);
    }

    public function test_unread_count(): void
    {
        $user = $this->createUser();
        $other = User::factory()->create(['tenant_id' => 'eba']);
        $this->actingAs($user);

        Message::factory()->create([
            'sender_id' => $other->id,
            'receiver_id' => $user->id,
            'read_at' => null,
        ]);

        Livewire::test(Messagerie::class)
            ->assertSee('1');
    }

    public function test_selecting_user_marks_conversation_as_read(): void
    {
        $alice = $this->createUser();
        $bob = User::factory()->create(['tenant_id' => 'eba']);

        Message::factory()->create([
            'sender_id' => $bob->id,
            'receiver_id' => $alice->id,
            'read_at' => null,
        ]);

        $this->actingAs($alice);

        Livewire::test(Messagerie::class)
            ->call('selectUser', $bob->id);

        $msg = Message::where('receiver_id', $alice->id)
            ->where('sender_id', $bob->id)
            ->first();
        $this->assertNotNull($msg->read_at);
    }

    public function test_toggle_panel(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        Livewire::test(Messagerie::class)
            ->assertSet('showPanel', false)
            ->call('togglePanel')
            ->assertSet('showPanel', true)
            ->call('togglePanel')
            ->assertSet('showPanel', false);
    }
}
