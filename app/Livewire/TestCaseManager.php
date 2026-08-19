<?php

namespace App\Livewire;

use App\Imports\ProjectExcelImport;
use App\Models\Message;
use App\Models\Project;
use App\Models\Report;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use App\Support\ProjectAccess;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public bool $showDevModal = false;

    public int $version = 0;

    public array $selectedDevIds = [];

    public bool $showArchives = false;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
        $this->selectedDevIds = $project->developers()->pluck('users.id')->map(fn ($id) => (string) $id)->toArray();
    }

    #[On('openArchives')]
    public function openArchives(): void
    {
        $this->showArchives = true;
    }

    #[Computed]
    public function developersList()
    {
        return User::role('developer')->orderBy('name')->get();
    }

    #[Computed]
    public function templates()
    {
        $query = TestCaseTemplate::where('project_id', $this->project->id)
            ->withCount('testCases')
            ->latest();

        $user = auth()->user();

        if ($user->hasRole('tester') || $user->hasRole('client')) {
            if (! ProjectAccess::isAssignedToProject($user, $this->project->id)) {
                $assignedTemplateIds = TestCaseAssignment::where('user_id', $user->id)
                    ->whereNotNull('template_id')
                    ->pluck('template_id')
                    ->toArray();

                $query->whereIn('id', $assignedTemplateIds);
            }
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
        $this->authorize('create', TestCaseTemplate::class);

        $this->reset(['name', 'links', 'editMode', 'templateIdToEdit']);
        $this->showModal = true;
    }

    public function editTemplate($id): void
    {
        $template = TestCaseTemplate::findOrFail($id);
        $this->authorize('update', $template);

        $this->templateIdToEdit = $template->id;
        $this->name = $template->name;
        $this->links = is_array($template->links) ? $template->links : [];
        $this->editMode = true;
        $this->showModal = true;
    }

    public function deleteTemplate($id): void
    {
        $template = TestCaseTemplate::findOrFail($id);
        $this->authorize('delete', $template);

        $count = $template->testCases()->count();
        $template->testCases()->delete();
        $template->delete();

        $message = $count > 0
            ? "Fichier et {$count} cas de test supprimé(s) avec succès."
            : 'Cas de test supprimé avec succès.';

        $this->version++;
        session()->flash('success', $message);
    }

    public function save(): void
    {
        if ($this->editMode && $this->templateIdToEdit) {
            $this->authorize('update', TestCaseTemplate::findOrFail($this->templateIdToEdit));
        } else {
            $this->authorize('create', TestCaseTemplate::class);
        }

        $this->validate([
            'name' => 'required|min:3|max:255',
            'links.*.title' => 'required|string',
            'links.*.url' => 'required|url',
        ]);

        if ($this->editMode && $this->templateIdToEdit) {
            $template = TestCaseTemplate::findOrFail($this->templateIdToEdit);
            $template->update([
                'name' => $this->name,
                'links' => $this->links,
            ]);
            session()->flash('success', 'Cas de test mis à jour avec succès.');
        } else {
            TestCaseTemplate::create([
                'name' => $this->name,
                'project_id' => $this->project->id,
                'fields' => TestCaseTemplate::defaultFields(),
                'links' => $this->links,
            ]);
            session()->flash('success', 'Cas de test créé avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'links', 'editMode', 'templateIdToEdit']);
    }

    public function promoteToUat(): void
    {
        $this->authorize('update', $this->project);

        $this->project->update(['type' => 'uat']);

        TestCase::where('project_id', $this->project->id)->update(['type' => 'uat']);

        session()->flash('success', 'Le projet est passé en phase UAT avec succès. Les clients peuvent désormais consulter et valider les cas de test.');
    }

    public function revertToIat(): void
    {
        $this->authorize('update', $this->project);

        $this->project->update([
            'type' => 'iat',
            'status' => 'in_progress',
        ]);

        TestCase::where('project_id', $this->project->id)
            ->update([
                'type' => 'iat',
                'client_status' => 'pending',
                'client_status_by' => auth()->id(),
            ]);

        foreach ($this->project->developers as $dev) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Le projet {$this->project->name} a été renvoyé en phase IAT suite aux retours du client.",
            ]);
        }

        $testerIds = TestCaseAssignment::where('project_id', $this->project->id)
            ->pluck('user_id')
            ->unique();

        foreach ($testerIds as $testerId) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $testerId,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Le projet {$this->project->name} a été renvoyé en phase IAT suite aux retours du client.",
            ]);
        }

        session()->flash('success', 'Le projet a été renvoyé en phase IAT pour de nouveaux tests (le statut des tests clients a été réinitialisé).');
    }

    public function openDevModal(): void
    {
        $this->authorize('update', $this->project);

        $this->selectedDevIds = $this->project->developers()->pluck('users.id')->map(fn ($id) => (string) $id)->toArray();
        $this->showDevModal = true;
    }

    public function saveDevelopers(): void
    {
        $this->authorize('update', $this->project);

        $this->project->developers()->sync($this->selectedDevIds);
        $this->project->unsetRelation('developers');
        $this->showDevModal = false;
        session()->flash('success', 'Développeurs mis à jour avec succès.');
    }

    public function closeProject(): void
    {
        $this->authorize('update', $this->project);

        $this->project->update(['status' => 'completed']);
        session()->flash('success', 'Le projet a été clôturé avec succès et marqué comme terminé.');
    }

    public function reopenProject(): void
    {
        $this->authorize('update', $this->project);

        $this->project->update(['status' => 'in_progress']);
        session()->flash('success', 'Le projet a été réactivé avec succès.');
    }

    public function removeDeveloper(int $userId): void
    {
        $this->authorize('update', $this->project);

        $this->project->developers()->detach($userId);
        $this->project->unsetRelation('developers');
        $this->selectedDevIds = array_filter($this->selectedDevIds, fn ($id) => (int) $id !== $userId);
    }

    public function sendToDeveloper(): void
    {
        $this->authorize('update', $this->project);

        $this->project->update(['status' => 'in_review']);

        $developers = $this->project->developers;
        if ($developers->count() > 0) {
            foreach ($developers as $dev) {
                Message::create([
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
        $this->authorize('update', $this->project);

        $report = Report::findOrFail($reportId);
        $report->update(['status' => 'sent']);
        $this->project->update(['status' => 'in_review']);

        $developers = $this->project->developers;
        $stats = $report->stats ?? [];

        $reportText = "EXECUTIVE REPORT {$this->project->name}\n\n";
        $reportText .= "*Informations générales*\n";
        $reportText .= "Nom du projet : {$this->project->name}\n";
        $reportText .= "Version testée : {$report->tested_version}\n";
        $reportText .= 'Date du test : '.$report->created_at->format('d/m/Y')."\n";
        $reportText .= "Responsable du test : {$report->responsible}\n";
        $reportText .= "Périmètre du test : {$report->perimeter}\n";
        $reportText .= "Nombre total des cas de test : {$stats['total']} cas de test\n\n";

        $reportText .= "*Statistiques globales*\n";
        $reportText .= "succes : {$stats['valide']}\n";
        $reportText .= "échec : {$stats['non_valide']}\n";
        $reportText .= "sous reserve : {$stats['sous_reserve']}\n";
        $reportText .= "optimisation : {$stats['optimisation']}\n\n";

        $reportText .= "NB : {$report->notes}\n";
        $reportText .= 'Lien pour plus de détails : '.route('projets.show', $this->project->id);

        if ($developers->count() > 0) {
            foreach ($developers as $dev) {
                Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $dev->id,
                    'project_id' => $this->project->id,
                    'type' => 'system',
                    'content' => "Le rapport de test {$report->perimeter} vous a été transféré :\n\n".$reportText,
                ]);
            }
            session()->flash('success', 'Le rapport a été transféré aux développeurs avec succès.');
        } else {
            session()->flash('error', "Le rapport est marqué comme envoyé, mais aucun développeur n'est assigné à ce projet.");
        }
    }

    public function validateCorrection(int $reportId): void
    {
        $this->authorize('update', $this->project);

        $report = Report::findOrFail($reportId);
        $report->update(['status' => 'closed']);

        $pendingReports = Report::where('project_id', $this->project->id)
            ->whereIn('status', ['sent', 'resolved', 'retest'])
            ->count();

        if ($pendingReports === 0) {
            $this->project->update(['status' => 'completed']);
        }

        foreach ($this->project->developers as $dev) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => 'Le chef de projet '.auth()->user()->name." a validé la correction sur le rapport {$report->perimeter}. Merci !",
            ]);
        }

        session()->flash('success', 'Correction validée. Les développeurs ont été notifiés.');
    }

    public function rejectToTester(int $reportId): void
    {
        $this->authorize('update', $this->project);

        $report = Report::findOrFail($reportId);
        $report->update(['status' => 'retest']);

        $testerIds = TestCaseAssignment::where('project_id', $this->project->id)
            ->pluck('user_id')
            ->unique();

        $testers = User::whereIn('id', $testerIds)->get();

        foreach ($testers as $tester) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $tester->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => 'Le chef de projet '.auth()->user()->name." vous demande de re-tester le projet {$this->project->name} suite à une correction sur le rapport {$report->perimeter}.",
            ]);
        }

        foreach ($this->project->developers as $dev) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Votre correction sur le rapport {$report->perimeter} va être re-testée par l'équipe de test.",
            ]);
        }

        session()->flash('success', 'Rapport renvoyé au testeur pour re-test. Les testeurs et développeurs ont été notifiés.');
    }

    public function relaunchDev(int $reportId): void
    {
        $this->authorize('update', $this->project);

        $report = Report::findOrFail($reportId);
        $report->update(['status' => 'sent']);
        $this->project->update(['status' => 'in_review']);

        foreach ($this->project->developers as $dev) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $dev->id,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => 'Le chef de projet '.auth()->user()->name." a vérifié et constate que la correction n'est pas satisfaisante sur le rapport {$report->perimeter}. Merci de corriger à nouveau.",
            ]);
        }

        session()->flash('success', 'Rapport relancé auprès des développeurs.');
    }

    public function updatedGlobalExcelFile(): void
    {
        $this->importGlobalExcel();
    }

    public function importGlobalExcel(): void
    {
        $this->authorize('update', $this->project);

        $this->validate([
            'globalExcelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $path = $this->globalExcelFile->getRealPath();

            $importService = new ProjectExcelImport($this->project->id);
            $results = $importService->import($path);

            $this->reset('globalExcelFile');

            if ($results['sheets'] > 0) {
                session()->flash('success', "Import réussi : {$results['sheets']} feuilles et {$results['rows']} cas de test importés.");
            } else {
                session()->flash('error', 'Aucune donnée valide trouvée dans ce fichier.');
            }
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'import : ".$e->getMessage());
        }
    }

    public function exportResults()
    {
        $this->authorize('view', $this->project);

        return redirect()->route('projets.export', $this->project->id);
    }

    public function render()
    {
        return view('livewire.test-case-manager', [
            'totalTestCases' => TestCase::where('project_id', $this->project->id)->count(),
        ]);
    }
}
