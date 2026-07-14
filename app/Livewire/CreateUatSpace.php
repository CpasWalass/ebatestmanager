<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User;
use App\Models\TestCaseAssignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class CreateUatSpace extends Component
{
    public bool $showModal = false;
    public ?Project $project = null;
    
    public string $clientName = '';
    public string $clientEmail = '';
    
    public string $generatedLink = '';
    public string $generatedPassword = '';

    #[On('openUatModal')]
    public function openModal($projectId): void
    {
        $this->project = Project::find($projectId);
        $this->showModal = true;
        $this->generatedLink = '';
        $this->generatedPassword = '';
    }

    public function createSpace(): void
    {
        $this->validate([
            'clientName' => 'required|string|min:3',
            'clientEmail' => 'required|email',
        ]);

        $this->generatedPassword = Str::random(10);

        // Check if user exists
        $user = User::where('email', $this->clientEmail)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $this->clientName,
                'email' => $this->clientEmail,
                'password' => Hash::make($this->generatedPassword),
                'tenant_id' => $this->project->tenant_id,
            ]);
            $user->assignRole('client');
        } else {
            // Update password for simplicity in this demo, or keep it as is
            $user->update(['password' => Hash::make($this->generatedPassword)]);
        }

        // Assign project to client
        $assignment = TestCaseAssignment::firstOrCreate([
            'project_id' => $this->project->id,
            'user_id' => $user->id,
        ], [
            'scope' => 'full_case',
            'status' => 'pending',
            'tenant_id' => $this->project->tenant_id,
        ]);

        $this->generatedLink = route('client.dashboard');
        
        // Envoi d'un message interne au client
        if ($assignment->wasRecentlyCreated) {
            \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $user->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Bienvenue ! L'espace de recette (UAT) pour le projet {$this->project->name} est prêt. [Cliquez ici pour y accéder]({$this->generatedLink})",
            ]);
        }
        
        session()->flash('success', 'Espace UAT créé avec succès et notification envoyée.');
    }

    public function render()
    {
        return view('livewire.create-uat-space');
    }
}
