<?php

namespace App\Livewire;

use App\Mail\WelcomeNewUser;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TeamManager extends Component
{
    public string $search = '';

    public string $roleFilter = '';

    public bool $showModal = false;

    public string $name = '';

    public string $email = '';

    public string $role = 'tester';

    public string $generatedPassword = '';

    public int $assignationVersion = 0;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    #[Computed]
    public function users()
    {
        $user = auth()->user();

        return User::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
            )
            ->when($this->roleFilter, fn ($q) => $q->role($this->roleFilter))
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->each(function ($member) {
                if (! $member->hasRole('tester')) {
                    $member->test_cases_count = 0;
                    $member->executed_count = 0;

                    return;
                }

                $assignedProjectIds = TestCaseAssignment::where('user_id', $member->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->toArray();

                $assignedTemplateIds = TestCaseAssignment::where('user_id', $member->id)
                    ->whereNotNull('template_id')
                    ->pluck('template_id')
                    ->toArray();

                $activeProjectIds = Project::whereNotIn('status', ['completed', 'archived'])
                    ->pluck('id')
                    ->toArray();

                $assignedCaseIds = TestCase::whereIn('project_id', $activeProjectIds)
                    ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                        $q->whereIn('project_id', $assignedProjectIds)
                            ->orWhereIn('template_id', $assignedTemplateIds);
                    })
                    ->pluck('id');

                $member->test_cases_count = $assignedCaseIds->count();

                $member->executed_count = TestCase::whereIn('id', $assignedCaseIds)
                    ->whereIn('progress', ['termine', 'bloque'])
                    ->count();
            });
    }

    public function openNewModal(): void
    {
        $this->authorize('create', User::class);

        $this->reset(['name', 'email', 'role']);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $this->email = strtolower(trim($this->email));

        $this->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(fn ($q) => $q->whereRaw('LOWER(email) = ?', [$this->email])),
            ],
            'role' => 'required|in:chef_project,tester,developer,client',
        ]);

        $temporaryPassword = Str::random(12);

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $temporaryPassword,
                'tenant_id' => auth()->user()->tenant_id,
                'is_active' => true,
                'must_change_password' => true,
                'temporary_password_hash' => Hash::make($temporaryPassword),
            ]);
        } catch (QueryException $e) {
            $this->addError('email', 'Un utilisateur avec cette adresse e-mail existe déjà.');

            return;
        }

        $user->assignRole($this->role);

        Mail::to($user->email)->send(new WelcomeNewUser($user, $temporaryPassword));

        $this->showModal = false;
        $this->reset(['name', 'email', 'role']);
        session()->flash('success', 'Utilisateur créé avec succès. Un email de création a été envoyé avec un mot de passe temporaire.');
    }

    public function resendCredentials(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            return;
        }

        $temporaryPassword = Str::random(12);

        $user->password = $temporaryPassword;
        $user->temporary_password_hash = Hash::make($temporaryPassword);
        $user->must_change_password = true;
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        Mail::to($user->email)->send(new WelcomeNewUser($user, $temporaryPassword));

        session()->flash('success', "Nouveaux identifiants envoyés à {$user->name} ({$user->email}).");
    }

    public function toggleActiveStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        if ($user->id !== auth()->id()) {
            $user->is_active = ! $user->is_active;
            $user->save();

            $status = $user->is_active ? 'réactivé' : 'désactivé';
            session()->flash('success', "Le compte de {$user->name} a été {$status}.");
        }
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        $user->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }

    #[On('assignments-updated')]
    public function refreshUsers(): void
    {
        $this->assignationVersion++;
    }

    public function render()
    {
        return view('livewire.team-manager');
    }
}
