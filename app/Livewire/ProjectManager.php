<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

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

    public string $type = 'iat';

    public $client_id = null;

    public array $links = [];

    public array $developer_ids = [];

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get();
    }

    #[Computed]
    public function developersList()
    {
        return User::role('developer')->orderBy('name')->get();
    }

    #[Computed]
    public function projects()
    {
        $query = Project::where('name', 'like', '%'.$this->search.'%')
            ->withCount('testCases')
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $user = auth()->user();

        if ($user->hasRole('tester') || $user->hasRole('client')) {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $user->id)
                ->whereNotNull('project_id')
                ->pluck('project_id')
                ->toArray();

            $assignedTemplateProjectIds = TestCaseTemplate::whereIn('id', function ($q) use ($user) {
                $q->select('template_id')
                    ->from('test_case_assignments')
                    ->where('user_id', $user->id)
                    ->whereNotNull('template_id');
            })->pluck('project_id')->toArray();

            $allAssignedProjectIds = array_unique(array_merge($assignedProjectIds, $assignedTemplateProjectIds));

            $query->whereIn('id', $allAssignedProjectIds);

            // Un client ne voit que les projets UAT (auparavant comparé à 'UAT' en
            // majuscules, incohérent avec la colonne désormais toujours en minuscules).
            if ($user->hasRole('client')) {
                $query->where('type', 'uat');
            }
        }

        if ($user->hasRole('developer')) {
            $query->whereHas('developers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->get();
    }

    public function addLink(): void
    {
        $this->links[] = ['title' => '', 'url' => ''];
    }

    public function removeLink($index): void
    {
        unset($this->links[$index]);
        $this->links = array_values($this->links);
    }

    public function openNewModal(): void
    {
        $this->authorize('create', Project::class);

        $this->reset(['name', 'description', 'version', 'perimeter', 'client_id', 'links', 'developer_ids', 'editMode', 'projectIdToEdit']);
        $this->type = 'iat';
        $this->showModal = true;
    }

    public function editProject($id): void
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $this->projectIdToEdit = $project->id;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->version = $project->version ?? '';
        $this->perimeter = $project->perimeter ?? '';
        $this->type = $project->type ?? 'iat';
        $this->client_id = $project->client_id;
        $this->links = is_array($project->links) ? $project->links : [];
        $this->developer_ids = $project->developers()->pluck('users.id')->toArray();
        $this->editMode = true;
        $this->showModal = true;
    }

    public function deleteProject($id): void
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);

        $project->delete();
        session()->flash('success', 'Projet supprimé avec succès.');
    }

    public function reopenProject(int $projectId): void
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('update', $project);

        $project->update(['status' => 'in_progress']);
        session()->flash('success', 'Le projet a été réactivé avec succès.');
    }

    /**
     * IMPORTANT — correctif de sécurité : aucune de ces méthodes ne vérifiait
     * l'autorisation côté serveur dans la version originale. Le bouton
     * "Modifier"/"Supprimer" était bien masqué côté vue pour les rôles autres
     * que chef_project (@if(hasRole('chef_project'))), mais masquer un bouton
     * n'empêche pas un appel direct à la méthode Livewire (wire:click envoie
     * une requête AJAX vers le nom de la méthode ; rien ne garantissait qu'un
     * testeur ou un client ne pouvait pas déclencher save()/deleteProject()
     * en connaissant simplement le nom du composant). $this->authorize(...)
     * s'appuie sur ProjectPolicy (Phase 1) et fait échouer la requête avec une
     * 403 si l'utilisateur n'a pas le droit, indépendamment de ce que la vue affiche.
     */
    public function save(): void
    {
        if ($this->editMode && $this->projectIdToEdit) {
            $this->authorize('update', Project::findOrFail($this->projectIdToEdit));
        } else {
            $this->authorize('create', Project::class);
        }

        $this->validate([
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'perimeter' => 'nullable|string',
            'type' => 'required|in:iat,uat',
            'client_id' => 'required|exists:clients,id',
            'links.*.title' => 'required|string',
            'links.*.url' => 'required|url',
            'developer_ids' => 'nullable|array',
            'developer_ids.*' => 'exists:users,id',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
        ]);

        if ($this->editMode && $this->projectIdToEdit) {
            $project = Project::findOrFail($this->projectIdToEdit);
            $project->update([
                'name' => $this->name,
                'description' => $this->description,
                'version' => $this->version,
                'perimeter' => $this->perimeter,
                'type' => $this->type,
                'client_id' => $this->client_id,
                'links' => $this->links,
            ]);
            $project->developers()->sync($this->developer_ids);
            session()->flash('success', 'Projet mis à jour avec succès.');
        } else {
            $project = Project::create([
                'name' => $this->name,
                'description' => $this->description,
                'version' => $this->version,
                'perimeter' => $this->perimeter,
                'type' => $this->type,
                'client_id' => $this->client_id,
                'links' => $this->links,
                'created_by' => auth()->id(),
            ]);
            $project->developers()->sync($this->developer_ids);
            session()->flash('success', 'Projet créé avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'version', 'perimeter', 'client_id', 'links', 'developer_ids', 'editMode', 'projectIdToEdit']);
        $this->type = 'iat';
    }

    public function render()
    {
        return view('livewire.project-manager');
    }
}
