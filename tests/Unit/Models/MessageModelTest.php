<?php

namespace Tests\Unit\Models;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_read_sets_read_at(): void
    {
        $msg = Message::factory()->create(['read_at' => null]);
        $this->assertFalse($msg->isRead());

        $msg->markAsRead();
        $this->assertTrue($msg->fresh()->isRead());
        $this->assertNotNull($msg->fresh()->read_at);
    }

    public function test_mark_as_read_is_idempotent(): void
    {
        $now = now()->subHours(5);
        $msg = Message::factory()->create(['read_at' => $now]);

        $msg->markAsRead();
        $this->assertEquals($now->timestamp, $msg->fresh()->read_at->timestamp);
    }

    public function test_scope_unread_for(): void
    {
        $user = User::factory()->create();
        Message::factory()->create(['receiver_id' => $user->id, 'read_at' => null]);
        Message::factory()->create(['receiver_id' => $user->id, 'read_at' => now()]);
        Message::factory()->create(['read_at' => null]);

        $unread = Message::unreadFor($user->id)->get();
        $this->assertCount(1, $unread);
    }

    public function test_scope_conversation(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();

        Message::factory()->create(['sender_id' => $alice->id, 'receiver_id' => $bob->id]);
        Message::factory()->create(['sender_id' => $bob->id, 'receiver_id' => $alice->id]);
        Message::factory()->create(['sender_id' => $alice->id, 'receiver_id' => User::factory()->create()->id]);

        $conversation = Message::conversation($alice->id, $bob->id)->get();
        $this->assertCount(2, $conversation);
    }
}
