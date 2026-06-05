<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ProjectManager extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public string $name = '';
    public string $description = '';
    public string $version = '';
    public string $perimeter = '';
    public string $type = 'IAT';

    #[Computed]
    public function projects()
    {
        $query = Project::where('name', 'like', '%' . $this->search . '%')
            ->withCount('testCases')
            ->latest();
            
        if (auth()->check() && auth()->user()->hasRole('tester')) {
            $user = auth()->user();
            $assignedProjectIds = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                ->whereNotNull('project_id')
                ->pluck('project_id')
                ->toArray();
                
            $assignedTemplateProjectIds = \App\Models\TestCaseTemplate::whereIn('id', function($q) use ($user) {
                $q->select('template_id')
                  ->from('test_case_assignments')
                  ->where('user_id', $user->id)
                  ->whereNotNull('template_id');
            })->pluck('project_id')->toArray();
            
            $allAssignedProjectIds = array_unique(array_merge($assignedProjectIds, $assignedTemplateProjectIds));
            
            $query->whereIn('id', $allAssignedProjectIds);
        }
            
        return $query->get();
    }

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|min:3|max:255',
            'description' => 'nullable|string',
            'version'     => 'nullable|string|max:50',
            'perimeter'   => 'nullable|string',
            'type'        => 'required|string',
        ]);

        Project::create([
            'name'        => $this->name,
            'description' => $this->description,
            'version'     => $this->version,
            'perimeter'   => $this->perimeter,
            'type'        => $this->type,
            'created_by'  => auth()->id(),
        ]);

        $this->showModal = false;
        $this->reset(['name', 'description', 'version', 'perimeter', 'type']);
        session()->flash('success', 'Projet créé avec succès.');
    }

    public function render()
    {
        return view('livewire.project-manager');
    }
}
