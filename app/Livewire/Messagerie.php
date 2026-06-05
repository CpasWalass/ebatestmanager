<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Messagerie extends Component
{
    public bool $showPanel = false;
    public ?int $selectedUserId = null;
    public string $newMessage = '';

    // Toggle the slide-over panel
    public function togglePanel(): void
    {
        $this->showPanel = !$this->showPanel;
        if ($this->showPanel) {
            $this->markConversationAsRead();
        }
    }

    #[On('openMessagerie')]
    public function openFromNavbar(): void
    {
        $this->showPanel = true;
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->newMessage = '';
        $this->markConversationAsRead();
    }

    public function markConversationAsRead(): void
    {
        if ($this->selectedUserId) {
            Message::where('receiver_id', auth()->id())
                ->where('sender_id', $this->selectedUserId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        if (!$this->selectedUserId) return;

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $this->selectedUserId,
            'content'     => $this->newMessage,
            'type'        => 'direct',
        ]);

        $this->newMessage = '';
    }

    #[Computed]
    public function contacts()
    {
        // Get all users who are in the same team (same tenant) excluding self
        return User::where('id', '!=', auth()->id())
            ->where(function ($q) {
                $q->where('tenant_id', auth()->user()->tenant_id)
                  ->orWhereNull('tenant_id');
            })
            ->withCount([
                'sentMessages as unread_from' => function ($q) {
                    $q->where('receiver_id', auth()->id())
                      ->whereNull('read_at');
                }
            ])
            ->get()
            ->sortByDesc('unread_from');
    }

    #[Computed]
    public function conversation()
    {
        if (!$this->selectedUserId) return collect();

        return Message::conversation(auth()->id(), $this->selectedUserId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    #[Computed]
    public function unreadTotal()
    {
        return Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    #[Computed]
    public function selectedContact()
    {
        if (!$this->selectedUserId) return null;
        return User::find($this->selectedUserId);
    }

    public function render()
    {
        return view('livewire.messagerie');
    }
}
