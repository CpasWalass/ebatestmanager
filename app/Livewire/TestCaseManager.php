<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;

class TestCaseManager extends Component
{
    public Project $project;
    public bool $showModal = false;
    public string $name = '';
    public array $links = [];

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    #[Computed]
    public function templates()
    {
        $query = TestCaseTemplate::where('project_id', $this->project->id)
            ->withCount('testCases')
            ->latest();
            
        if (auth()->check() && auth()->user()->hasRole('tester')) {
            $user = auth()->user();
            
            // Si le testeur est assigné à TOUT le projet, il voit tous les templates
            $assignedToProject = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                ->where('project_id', $this->project->id)
                ->exists();
                
            if (!$assignedToProject) {
                // Sinon on filtre pour n'afficher que les templates auxquels il est spécifiquement assigné
                $assignedTemplateIds = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                    ->whereNotNull('template_id')
                    ->pluck('template_id')
                    ->toArray();
                    
                $query->whereIn('id', $assignedTemplateIds);
            }
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
            'name' => 'required|min:3|max:255',
            'links.*.title' => 'required|string',
            'links.*.url'   => 'required|url',
        ]);

        TestCaseTemplate::create([
            'name'       => $this->name,
            'project_id' => $this->project->id,
            'fields'     => TestCaseTemplate::defaultFields(),
            'links'      => $this->links,
        ]);

        $this->showModal = false;
        $this->reset(['name', 'links']);
        session()->flash('success', 'Cas de test créé avec succès.');
    }

    public function sendToDeveloper(): void
    {
        $this->project->update(['status' => 'in_review']);
        session()->flash('success', 'Projet envoyé au développeur pour correction.');
    }

    public function render()
    {
        return view('livewire.test-case-manager');
    }
}
