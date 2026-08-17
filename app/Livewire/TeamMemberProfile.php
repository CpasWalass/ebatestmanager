<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class TeamMemberProfile extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->authorize('view', $user);

        // Simplifié : un seul tenant réel pour l'instant (voir README_REFONTE.md,
        // Phase 1) — plus de vérification tenant_id manuelle nécessaire ici, la
        // policy suffit.

        // Corrigé : la relation s'appelle assignments() sur User (Phase 1),
        // pas testCaseAssignments() — ce chargement échouait silencieusement
        // avant (Eloquent ignore une relation eager-load inexistante sans
        // erreur, la vue affichait juste une liste vide).
        $this->user = $user->load([
            'roles',
            'assignments.project',
            'assignments.template',
            'projectsAsDeveloper',
            'projects',
            'testExecutions.testCase.template',
            'testExecutions.assignment.project',
        ]);
    }

    public function toggleActiveStatus()
    {
        $this->authorize('update', $this->user);

        if ($this->user->id !== auth()->id()) {
            $this->user->is_active = ! $this->user->is_active;
            $this->user->save();

            $status = $this->user->is_active ? 'réactivé' : 'désactivé';
            session()->flash('success', "Le compte de {$this->user->name} a été {$status}.");
        }
    }

    public function render()
    {
        return view('livewire.team-member-profile')
            ->extends('layouts.app')
            ->section('content');
    }
}
