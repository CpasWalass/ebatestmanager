<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class AssignTesters extends Component
{
    public bool $showModal = false;
    public ?Project $project = null;
    public ?TestCaseTemplate $template = null;
    
    // Checkboxes array for testers: [userId => boolean]
    public array $selectedTesters = [];

    #[On('openAssignModal')]
    public function openModal($projectId = null, $templateId = null): void
    {
        $this->showModal = true;
        $this->selectedTesters = [];
        
        if ($projectId) {
            $this->project = Project::find($projectId);
        }
        
        if ($templateId) {
            $this->template = TestCaseTemplate::find($templateId);
        }
        
        // Load already assigned testers
        if ($this->template) {
            $assignedIds = TestCaseAssignment::where('template_id', $this->template->id)->pluck('user_id')->toArray();
        } elseif ($this->project) {
            $assignedIds = TestCaseAssignment::where('project_id', $this->project->id)->pluck('user_id')->toArray();
        } else {
            $assignedIds = [];
        }
        
        foreach ($assignedIds as $id) {
            $this->selectedTesters[$id] = true;
        }
    }

    #[Computed]
    public function testers()
    {
        return User::role('tester')
            ->withCount(['assignments as workload' => function ($query) {
                $query->where('status', 'pending')->orWhere('status', 'in_progress');
            }])
            ->orderBy('workload', 'asc')
            ->get();
    }

    public function save(): void
    {
        $selectedIds = array_keys(array_filter($this->selectedTesters));
        
        if ($this->template) {
            // Assign to template
            TestCaseAssignment::where('template_id', $this->template->id)->whereNotIn('user_id', $selectedIds)->delete();
            
            foreach ($selectedIds as $userId) {
                TestCaseAssignment::firstOrCreate([
                    'template_id' => $this->template->id,
                    'user_id' => $userId,
                ], [
                    'scope' => 'full_case',
                    'status' => 'pending',
                ]);
            }
        } elseif ($this->project) {
            // Assign to project
            TestCaseAssignment::where('project_id', $this->project->id)->whereNotIn('user_id', $selectedIds)->delete();
            
            foreach ($selectedIds as $userId) {
                TestCaseAssignment::firstOrCreate([
                    'project_id' => $this->project->id,
                    'user_id' => $userId,
                ], [
                    'scope' => 'full_case',
                    'status' => 'pending',
                ]);
            }
        }

        $this->showModal = false;
        session()->flash('success', 'Testeurs assignés avec succès.');
    }

    public function render()
    {
        return view('livewire.assign-testers');
    }
}
