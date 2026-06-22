<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use App\Models\TestCase;
use App\Models\Report;
use Livewire\Component;
use Livewire\Attributes\On;

class ReportGenerator extends Component
{
    public bool $showModal = false;
    public ?Project $project = null;
    public ?TestCaseTemplate $template = null;
    
    public string $perimeter = '';
    public string $testedVersion = '';
    public string $conclusion = '';
    
    public array $stats = [
        'total' => 0,
        'valide' => 0,
        'non_valide' => 0,
        'sous_reserve' => 0,
        'optimisation' => 0,
    ];

    #[On('openReportModal')]
    public function openModal($projectId, $templateId = null): void
    {
        $this->project = Project::find($projectId);
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
        
        $this->calculateStats($cases);
        $this->showModal = true;
    }

    private function calculateStats($cases): void
    {
        $this->stats['total'] = $cases->count();
        $this->stats['valide'] = 0;
        $this->stats['non_valide'] = 0;
        $this->stats['sous_reserve'] = 0;
        $this->stats['optimisation'] = 0;

        foreach ($cases as $case) {
            $status = strtolower($case->data['status'] ?? $case->data['etat_test'] ?? '');
            if (in_array($status, ['validé', 'terminé', 'valide', 'termine'])) {
                $this->stats['valide']++;
            } elseif (in_array($status, ['non validé', 'échec', 'non valide', 'echec'])) {
                $this->stats['non_valide']++;
            } elseif (in_array($status, ['sous réserve', 'sous reserve'])) {
                $this->stats['sous_reserve']++;
            } elseif (in_array($status, ['optimisation', 'en cours', 'a faire', 'à faire'])) {
                $this->stats['optimisation']++;
            }
        }
    }

    public function generateReport(): void
    {
        $this->validate([
            'perimeter' => 'required|min:3',
            'testedVersion' => 'nullable|string|max:255',
            'conclusion' => 'required|min:10',
        ]);

        $reportTitle = 'EXECUTIVE REPORT ' . $this->project->name . ($this->template ? ' - ' . $this->template->name : '');

        $report = Report::create([
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'title' => $reportTitle,
            'perimeter' => $this->perimeter,
            'tested_version' => $this->testedVersion,
            'test_date' => now(),
            'responsible' => auth()->user()->name,
            'notes' => $this->conclusion,
            'stats' => $this->stats,
            'status' => 'submitted'
        ]);

        // Notify Project Manager
        if ($this->project->createdBy) {
            \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $this->project->created_by,
                'project_id' => $this->project->id,
                'type' => 'system',
                'content' => "Un nouveau rapport d'exécution **({$this->perimeter})** a été généré par **" . auth()->user()->name . "** pour le projet {$this->project->name}."
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
