<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ProjectManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public bool $showModal = false;
    public bool $editMode = false;
    public $projectIdToEdit = null;
    
    public string $name = '';
    public string $description = '';
    public string $version = '';
    public string $perimeter = '';
    public string $type = 'IAT';
    public $client_id = null;
    public array $links = [];
    public array $developer_ids = [];

    #[Computed]
    public function clients()
    {
        return \App\Models\Client::orderBy('name')->get();
    }

    #[Computed]
    public function developersList()
    {
        return \App\Models\User::role('developpeur')->orderBy('name')->get();
    }

    #[Computed]
    public function projects()
    {
        $query = Project::where('name', 'like', '%' . $this->search . '%')
            ->withCount('testCases')
            ->latest();
            
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
            
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

    public function openNewModal()
    {
        $this->reset(['name', 'description', 'version', 'perimeter', 'type', 'client_id', 'links', 'developer_ids', 'editMode', 'projectIdToEdit']);
        $this->showModal = true;
    }

    public function editProject($id)
    {
        $project = Project::findOrFail($id);
        $this->projectIdToEdit = $project->id;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->version = $project->version ?? '';
        $this->perimeter = $project->perimeter ?? '';
        $this->type = $project->type ?? 'IAT';
        $this->client_id = $project->client_id;
        $this->links = is_array($project->links) ? $project->links : [];
        $this->developer_ids = $project->developers()->pluck('users.id')->toArray();
        $this->editMode = true;
        $this->showModal = true;
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        session()->flash('success', 'Projet supprimé avec succès.');
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
            'developer_ids' => 'nullable|array',
            'developer_ids.*' => 'exists:users,id',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
        ]);

        if ($this->editMode && $this->projectIdToEdit) {
            $project = Project::findOrFail($this->projectIdToEdit);
            $project->update([
                'name'        => $this->name,
                'description' => $this->description,
                'version'     => $this->version,
                'perimeter'   => $this->perimeter,
                'type'        => $this->type,
                'client_id'   => $this->client_id,
                'links'       => $this->links,
            ]);
            $project->developers()->sync($this->developer_ids);
            session()->flash('success', 'Projet mis à jour avec succès.');
        } else {
            $project = Project::create([
                'name'        => $this->name,
                'description' => $this->description,
                'version'     => $this->version,
                'perimeter'   => $this->perimeter,
                'type'        => $this->type,
                'client_id'   => $this->client_id,
                'links'       => $this->links,
                'created_by'  => auth()->id(),
            ]);
            $project->developers()->sync($this->developer_ids);
            session()->flash('success', 'Projet créé avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'version', 'perimeter', 'type', 'client_id', 'links', 'developer_ids', 'editMode', 'projectIdToEdit']);
    }

    public function render()
    {
        return view('livewire.project-manager');
    }
}
