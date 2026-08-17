<?php

namespace App\Livewire;

use App\Models\Message;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    public $recentMessages = [];

    protected $listeners = ['messageReceived' => 'refreshNotifications', 'messageRead' => 'refreshNotifications'];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        if (auth()->check()) {
            $this->unreadCount = Message::where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->count();

            $this->recentMessages = Message::where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->with('sender')
                ->latest()
                ->take(5)
                ->get();
        }
    }

    public function markAsRead($messageId)
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id === auth()->id()) {
            $message->markAsRead();
            $this->refreshNotifications();
            // Also notify the main messagerie if it's open
            $this->dispatch('messageRead', $messageId);
        }
    }

    public function openMessagerie()
    {
        $this->dispatch('openMessagerie');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
