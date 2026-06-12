<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class TeamMemberProfile extends Component
{
    public User $user;

    public function mount(User $user)
    {
        // On s'assure que le chef de projet ne voit que les utilisateurs de son tenant
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Accès non autorisé.');
        }
        
        $this->user = $user->load(['roles', 'testCaseAssignments.project', 'testCaseAssignments.template']);
    }

    public function toggleActiveStatus()
    {
        if ($this->user->id !== auth()->id()) {
            $this->user->is_active = !$this->user->is_active;
            $this->user->save();
            
            $status = $this->user->is_active ? 'réactivé' : 'désactivé';
            session()->flash('success', "Le compte de {$this->user->name} a été {$status}.");
        }
    }

    public function render()
    {
        return view('livewire.team-member-profile')
            ->layout('layouts.app');
    }
}
