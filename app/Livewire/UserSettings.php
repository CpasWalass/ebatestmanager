<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserSettings extends Component
{
    public $sessions = [];

    public function mount()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $this->sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'agent' => $this->createAgent($session->user_agent),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'id' => $session->id,
                ];
            });
    }

    protected function createAgent($userAgent)
    {
        // Simple parser
        $browser = 'Navigateur inconnu';
        if (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/Edg/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';

        $os = 'Système inconnu';
        if (preg_match('/Windows/i', $userAgent)) $os = 'Windows';
        elseif (preg_match('/Mac/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/Linux/i', $userAgent)) $os = 'Linux';
        elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
        elseif (preg_match('/iPhone|iPad/i', $userAgent)) $os = 'iOS';

        return (object) [
            'is_desktop' => in_array($os, ['Windows', 'macOS', 'Linux']),
            'platform' => $os,
            'browser' => $browser,
        ];
    }

    public function logoutOtherBrowserSessions()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        $this->loadSessions();
        session()->flash('success_sessions', 'Les autres sessions ont été déconnectées avec succès.');
    }

    public function render()
    {
        return view('livewire.user-settings')->layout('layouts.app');
    }
}
