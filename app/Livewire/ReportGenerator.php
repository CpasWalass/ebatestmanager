<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Project;
use App\Models\Report;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportGenerator extends Component
{
    public bool $showModal = false;

    public ?Project $project = null;

    public ?TestCaseTemplate $template = null;

    public string $perimeter = '';

    public string $testedVersion = '';

    public string $conclusion = '';

    public array $stats = [];

    #[On('openReportModal')]
    public function openModal($projectId, $templateId = null): void
    {
        $this->project = Project::findOrFail($projectId);
        $this->authorize('view', $this->project);

        $this->perimeter = '';
        $this->testedVersion = $this->project->version ?? '';
        $this->conclusion = '';

        if ($templateId) {
            $this->template = TestCaseTemplate::find($templateId);
            $this->perimeter = $this->template->name;
            $cases = TestCase::where('template_id', $this->template->id)->get();
        } else {
            $this->perimeter = 'Projet Complet';
            $cases = TestCase::where('project_id', $this->project->id)->get();
        }

        // Avant : recalcul indépendant ici (liste de mots-clés incomplète —
        // les cas "Bloqué" par exemple n'apparaissaient dans aucun compteur,
        // ce qui pouvait faire un total affiché différent de la somme des
        // catégories dans le rapport généré). Utilise désormais la même
        // source que les dashboards : TestCase::statsFor().
        $this->stats = TestCase::statsFor($cases);

        $this->showModal = true;
    }

    public function rules(): array
    {
        return [
            'perimeter' => 'required|min:3',
            'testedVersion' => 'nullable|string|max:255',
            'conclusion' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'perimeter.required' => 'Le périmètre du test est obligatoire.',
            'perimeter.min' => 'Le périmètre doit contenir au moins 3 caractères.',
        ];
    }

    public function generateReport(): void
    {
        $this->authorize('view', $this->project);
        $this->validate();

        $reportTitle = 'EXECUTIVE REPORT '.$this->project->name.($this->template ? ' - '.$this->template->name : '');

        $existingReport = Report::where('project_id', $this->project->id)
            ->where('perimeter', $this->perimeter)
            ->where('status', 'retest')
            ->first();

        if ($existingReport) {
            $existingReport->update([
                'tested_version' => $this->testedVersion,
                'test_date' => now(),
                'notes' => $this->conclusion,
                'stats' => $this->stats,
                'status' => 'draft',
            ]);

            if ($this->project->createdBy) {
                Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $this->project->created_by,
                    'project_id' => $this->project->id,
                    'type' => 'system',
                    'content' => 'Le testeur '.auth()->user()->name." a mis à jour le rapport ({$this->perimeter}) suite à un re-test.",
                ]);
            }

            $this->showModal = false;
            session()->flash('success', 'Rapport de re-test mis à jour et renvoyé au Chef de Projet.');

            return;
        }

        Report::create([
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'title' => $reportTitle,
            'perimeter' => $this->perimeter,
            'tested_version' => $this->testedVersion,
            'test_date' => now(),
            'responsible' => auth()->user()->name,
            'notes' => $this->conclusion,
            'stats' => $this->stats,
            'status' => 'draft',
        ]);

        if ($this->project->createdBy) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $this->project->created_by,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Un nouveau rapport d'exécution ({$this->perimeter}) a été généré par ".auth()->user()->name." pour le projet {$this->project->name}.",
            ]);
        }

        $this->showModal = false;
        session()->flash('success', 'Rapport généré et envoyé au Chef de Projet avec succès.');
    }

    public function render()
    {
        return view('livewire.report-generator');
    }
}
