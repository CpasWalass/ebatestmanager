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
    
    public string $title = '';
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
        if ($templateId) {
            $this->template = TestCaseTemplate::find($templateId);
            $this->title = 'Rapport de Test - ' . $this->template->name;
            $cases = TestCase::where('template_id', $this->template->id)->get();
        } else {
            $this->title = 'Rapport Global - ' . $this->project->name;
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
            $status = strtolower($case->data['status'] ?? '');
            if (in_array($status, ['validé', 'terminé'])) {
                $this->stats['valide']++;
            } elseif (in_array($status, ['non validé', 'échec'])) {
                $this->stats['non_valide']++;
            } elseif (in_array($status, ['sous réserve'])) {
                $this->stats['sous_reserve']++;
            } elseif (in_array($status, ['optimisation', 'en cours'])) {
                $this->stats['optimisation']++;
            }
        }
    }

    public function generateReport(): void
    {
        $this->validate([
            'title' => 'required|min:3',
            'conclusion' => 'required|min:10',
        ]);

        Report::create([
            'project_id' => $this->project->id,
            'template_id' => $this->template?->id,
            'author_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->conclusion,
            'stats' => $this->stats,
            'status' => 'submitted'
        ]);

        $this->showModal = false;
        session()->flash('success', 'Rapport généré et envoyé au Chef de Projet avec succès.');
    }

    public function render()
    {
        return view('livewire.report-generator');
    }
}
