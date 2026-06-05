<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamManager extends Component
{
    public string $search = '';
    public string $roleFilter = '';
    public bool $showModal = false;

    // New user form
    public string $name = '';
    public string $email = '';
    public string $role = 'tester';
    public string $generatedPassword = '';

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->when($this->roleFilter, fn($q) => $q->role($this->roleFilter))
            ->with('roles')
            ->withCount([
                'assignments as active_assignments' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])
            ])
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:chef_project,tester,developer,client',
        ]);

        $this->generatedPassword = Str::random(10);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->generatedPassword),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $user->assignRole($this->role);

        $this->showModal = false;
        $this->reset(['name', 'email', 'role']);
        session()->flash('success', "Utilisateur créé. Mot de passe : {$this->generatedPassword}");
    }

    public function deleteUser(int $userId): void
    {
        $user = User::find($userId);
        if ($user && $user->id !== auth()->id()) {
            $user->delete();
            session()->flash('success', 'Utilisateur supprimé.');
        }
    }

    public function render()
    {
        return view('livewire.team-manager');
    }
}
