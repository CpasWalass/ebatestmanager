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
    public $client_id = null;
    public array $links = [];

    #[Computed]
    public function clients()
    {
        return \App\Models\Client::orderBy('name')->get();
    }

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

    public function addLink()
    {
        $this->links[] = ['title' => '', 'url' => ''];
    }

    public function removeLink($index)
    {
        unset($this->links[$index]);
        $this->links = array_values($this->links);
    }

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|min:3|max:255',
            'description' => 'nullable|string',
            'version'     => 'nullable|string|max:50',
            'perimeter'   => 'nullable|string',
            'type'        => 'required|string',
            'client_id'   => 'required|exists:clients,id',
            'links.*.title' => 'required|string',
            'links.*.url'   => 'required|url',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
        ]);

        Project::create([
            'name'        => $this->name,
            'description' => $this->description,
            'version'     => $this->version,
            'perimeter'   => $this->perimeter,
            'type'        => $this->type,
            'client_id'   => $this->client_id,
            'links'       => $this->links,
            'created_by'  => auth()->id(),
        ]);

        $this->showModal = false;
        $this->reset(['name', 'description', 'version', 'perimeter', 'type', 'client_id', 'links']);
        session()->flash('success', 'Projet créé avec succès.');
    }

    public function render()
    {
        return view('livewire.project-manager');
    }
}
