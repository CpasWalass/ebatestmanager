<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class TeamMemberProfile extends Component
{
    public User $user;

    public int $testerTotalCases = 0;

    public int $testerExecutedCases = 0;

    public function mount(User $user)
    {
        $this->authorize('view', $user);

        $this->user = $user->load([
            'roles',
            'assignments.project',
            'assignments.template',
            'projectsAsDeveloper',
            'projects',
            'testExecutions.testCase.template',
            'testExecutions.assignment.project',
        ]);

        $this->loadTesterStats();
    }

    private function loadTesterStats(): void
    {
        if (! $this->user->hasRole('tester')) {
            return;
        }

        $assignedProjectIds = TestCaseAssignment::where('user_id', $this->user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();

        $assignedTemplateIds = TestCaseAssignment::where('user_id', $this->user->id)
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

        $this->testerTotalCases = $assignedCaseIds->count();

        $this->testerExecutedCases = TestCase::whereIn('id', $assignedCaseIds)
            ->whereIn('progress', ['termine', 'bloque'])
            ->count();
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

    #[On('assignments-updated')]
    public function refreshAssignments(): void
    {
        $this->user->load(['assignments.project', 'assignments.template']);
        $this->loadTesterStats();
    }

    public function render()
    {
        return view('livewire.team-member-profile')
            ->extends('layouts.app')
            ->section('content');
    }
}
