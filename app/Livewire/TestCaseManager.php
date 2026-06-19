<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use App\Imports\ProjectExcelImport;

class TestCaseManager extends Component
{
    use WithFileUploads;

    public Project $project;
    public bool $showModal = false;
    public bool $editMode = false;
    public $templateIdToEdit = null;
    public string $name = '';
    public array $links = [];
    public $globalExcelFile;

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

    public function openNewModal()
    {
        $this->reset(['name', 'links', 'editMode', 'templateIdToEdit']);
        $this->showModal = true;
    }

    public function editTemplate($id)
    {
        $template = TestCaseTemplate::findOrFail($id);
        $this->templateIdToEdit = $template->id;
        $this->name = $template->name;
        $this->links = is_array($template->links) ? $template->links : [];
        $this->editMode = true;
        $this->showModal = true;
    }

    public function deleteTemplate($id)
    {
        $template = TestCaseTemplate::findOrFail($id);
        $template->delete();
        session()->flash('success', 'Cas de test supprimé avec succès.');
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'links.*.title' => 'required|string',
            'links.*.url'   => 'required|url',
        ]);

        if ($this->editMode && $this->templateIdToEdit) {
            $template = TestCaseTemplate::findOrFail($this->templateIdToEdit);
            $template->update([
                'name'  => $this->name,
                'links' => $this->links,
            ]);
            session()->flash('success', 'Cas de test mis à jour avec succès.');
        } else {
            TestCaseTemplate::create([
                'name'       => $this->name,
                'project_id' => $this->project->id,
                'fields'     => TestCaseTemplate::defaultFields(),
                'links'      => $this->links,
            ]);
            session()->flash('success', 'Cas de test créé avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'links', 'editMode', 'templateIdToEdit']);
    }

    public function sendToDeveloper(): void
    {
        $this->project->update(['status' => 'in_review']);
        
        // Notify assigned developers
        $developers = $this->project->developers;
        if ($developers->count() > 0) {
            foreach ($developers as $dev) {
                \App\Models\Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $dev->id,
                    'project_id' => $this->project->id,
                    'type' => 'system',
                    'content' => "Le projet **{$this->project->name}** vous a été envoyé pour correction (des anomalies ont été remontées).",
                ]);
            }
            session()->flash('success', 'Projet envoyé aux développeurs pour correction (notifications envoyées).');
        } else {
            session()->flash('success', 'Projet passé en statut correction (aucun développeur spécifique assigné à ce projet).');
        }
    }

    public function updatedGlobalExcelFile()
    {
        $this->importGlobalExcel();
    }

    public function importGlobalExcel()
    {
        $this->validate([
            'globalExcelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $path = $this->globalExcelFile->getRealPath();
            
            $importService = new ProjectExcelImport($this->project->id);
            $results = $importService->import($path);
            
            $this->reset('globalExcelFile');
            
            if ($results['sheets'] > 0) {
                session()->flash('success', "Import réussi : {$results['sheets']} feuilles et {$results['rows']} cas de test importés.");
            } else {
                session()->flash('error', "Aucune donnée valide trouvée dans ce fichier.");
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    public function exportResults()
    {
        try {
            $export = new \App\Exports\ProjectExcelExport($this->project);
            $path = $export->export();
            
            return response()->download(storage_path('app/' . $path))->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'export : " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.test-case-manager');
    }
}
