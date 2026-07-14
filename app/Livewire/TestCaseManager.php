<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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

    // Developer management
    public bool $showDevModal = false;
    public array $selectedDevIds = [];

    // Archives drawer
    public bool $showArchives = false;


    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->selectedDevIds = $project->developers()->pluck('users.id')->map(fn($id) => (string) $id)->toArray();
    }

    #[On('openArchives')]
    public function openArchives(): void
    {
        $this->showArchives = true;
    }

    #[Computed]
    public function developersList()
    {
        return \App\Models\User::role('developer')->orderBy('name')->get();
    }

    #[Computed]
    public function templates()
    {
        $query = TestCaseTemplate::where('project_id', $this->project->id)
            ->withCount('testCases')
            ->latest();

        // Filtre pour les testeurs ET les clients : ils ne voient que les templates qui leur sont assignés
        if (auth()->check() && (auth()->user()->hasRole('tester') || auth()->user()->hasRole('client'))) {
            $user = auth()->user();
            
            // Si l'utilisateur est assigné à TOUT le projet, il voit tous les templates
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

    public function promoteToUat(): void
    {
        $this->project->update(['type' => 'UAT']);
        
        // Also update all test cases to type UAT
        \App\Models\TestCase::where('project_id', $this->project->id)
            ->update(['type' => 'uat']);
            
        session()->flash('success', 'Le projet est passé en phase UAT avec succès. Les clients peuvent désormais consulter et valider les cas de test.');
    }

    public function revertToIat(): void
    {
        $this->project->update(['type' => 'IAT']);
        
        \App\Models\TestCase::where('project_id', $this->project->id)
            ->update([
                'type' => 'iat',
                'client_status' => 'pending'
            ]);

        // Notify testers and devs
        $developers = $this->project->developers;
        foreach ($developers as $dev) {
            \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Le projet {$this->project->name} a été renvoyé en phase IAT suite aux retours du client.",
            ]);
        }
            
        session()->flash('success', 'Le projet a été renvoyé en phase IAT pour de nouveaux tests (le statut des tests clients a été réinitialisé).');
    }

    public function openDevModal(): void
    {
        $this->selectedDevIds = $this->project->developers()->pluck('users.id')->map(fn($id) => (string) $id)->toArray();
        $this->showDevModal = true;
    }

    public function saveDevelopers(): void
    {
        $this->project->developers()->sync($this->selectedDevIds);
        $this->project->unsetRelation('developers');
        $this->showDevModal = false;
        session()->flash('success', 'Développeurs mis à jour avec succès.');
    }

    public function removeDeveloper(int $userId): void
    {
        $this->project->developers()->detach($userId);
        $this->project->unsetRelation('developers');
        $this->selectedDevIds = array_filter($this->selectedDevIds, fn($id) => (int) $id !== $userId);
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
                    'content' => "Le projet {$this->project->name} vous a été envoyé pour correction (des anomalies ont été remontées).",
                ]);
            }
            session()->flash('success', 'Projet envoyé aux développeurs pour correction (notifications envoyées).');
        } else {
            session()->flash('success', 'Projet passé en statut correction (aucun développeur spécifique assigné à ce projet).');
        }
    }

    public function sendReportToDev(int $reportId): void
    {
        $report = \App\Models\Report::findOrFail($reportId);
        $report->update(['status' => 'sent']);
        $this->project->update(['status' => 'in_review']);

        $developers = $this->project->developers;
        
        $stats = $report->stats;
        $reportText = "EXECUTIVE REPORT {$this->project->name}\n\n";
        $reportText .= "*Informations générales*\n";
        $reportText .= "Nom du projet : {$this->project->name}\n";
        $reportText .= "Version testée : {$report->tested_version}\n";
        $reportText .= "Date du test : " . $report->created_at->format('d/m/Y') . "\n";
        $reportText .= "Responsable du test : {$report->responsible}\n";
        $reportText .= "Périmètre du test : {$report->perimeter}\n";
        $reportText .= "Nombre total des cas de test : {$stats['total']} cas de test\n\n";
        
        $reportText .= "*Statistiques globales*\n";
        $reportText .= "succes : {$stats['valide']}\n";
        $reportText .= "échec : {$stats['non_valide']}\n";
        $reportText .= "sous reserve : {$stats['sous_reserve']}\n";
        $reportText .= "optimisation : {$stats['optimisation']}\n\n";
        
        $reportText .= "NB : {$report->notes}\n";
        $reportText .= "Lien pour plus de détails : " . route('projets.show', $this->project->id);

        if ($developers->count() > 0) {
            foreach ($developers as $dev) {
                \App\Models\Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $dev->id,
                    'project_id' => $this->project->id,
                    'type' => 'system',
                    'content' => "Le rapport de test {$report->perimeter} vous a été transféré :\n\n" . $reportText,
                ]);
            }
            session()->flash('success', 'Le rapport a été transféré aux développeurs avec succès.');
        } else {
            session()->flash('error', 'Le rapport est marqué comme envoyé, mais aucun développeur n\'est assigné à ce projet.');
        }
    }

    public function validateCorrection(int $reportId): void
    {
        $report = \App\Models\Report::findOrFail($reportId);
        $report->update(['status' => 'closed']);

        // Repasser le projet en statut actif si plus aucun rapport n'est en attente
        $pendingReports = \App\Models\Report::where('project_id', $this->project->id)
            ->whereIn('status', ['sent', 'resolved', 'retest'])
            ->count();

        if ($pendingReports === 0) {
            $this->project->update(['status' => 'completed']);
        }

        // Notifier les développeurs que la correction est validée
        $developers = $this->project->developers;
        foreach ($developers as $dev) {
            \App\Models\Message::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id'  => $this->project->id,
                'type'        => 'system',
                'content'     => "Le chef de projet " . auth()->user()->name . " a validé la correction sur le rapport {$report->perimeter}. Merci !",
            ]);
        }

        session()->flash('success', 'Correction validée. Les développeurs ont été notifiés.');
    }

    public function rejectToTester(int $reportId): void
    {
        $report = \App\Models\Report::findOrFail($reportId);
        $report->update(['status' => 'retest']);

        // Trouver les testeurs assignés au projet
        $testerIds = \App\Models\TestCaseAssignment::where('project_id', $this->project->id)
            ->pluck('user_id')
            ->unique();

        $testers = \App\Models\User::whereIn('id', $testerIds)->get();

        foreach ($testers as $tester) {
            \App\Models\Message::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $tester->id,
                'project_id'  => $this->project->id,
                'type'        => 'system',
                'content'     => "Le chef de projet " . auth()->user()->name . " vous demande de re-tester le projet {$this->project->name} suite à une correction sur le rapport {$report->perimeter}.",
            ]);
        }

        // Notifier les développeurs
        foreach ($this->project->developers as $dev) {
            \App\Models\Message::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id'  => $this->project->id,
                'type'        => 'system',
                'content'     => "Votre correction sur le rapport {$report->perimeter} va être re-testée par l'équipe de test.",
            ]);
        }

        session()->flash('success', 'Rapport renvoyé au testeur pour re-test. Les testeurs et développeurs ont été notifiés.');
    }

    public function relaunchDev(int $reportId): void
    {
        $report = \App\Models\Report::findOrFail($reportId);
        $report->update(['status' => 'sent']);
        $this->project->update(['status' => 'in_review']);

        $developers = $this->project->developers;
        foreach ($developers as $dev) {
            \App\Models\Message::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id'  => $this->project->id,
                'type'        => 'system',
                'content'     => "Le chef de projet " . auth()->user()->name . " a vérifié et constate que la correction n'est pas satisfaisante sur le rapport {$report->perimeter}. Merci de corriger à nouveau.",
            ]);
        }

        session()->flash('success', 'Rapport relancé auprès des développeurs.');
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
        return redirect()->route('projets.export', $this->project->id);
    }

    public function render()
    {
        return view('livewire.test-case-manager');
    }
}
