<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\User;
use App\Models\Message;
use App\Models\TestCaseTemplate;
use App\Models\TestCaseAssignment;
use App\Models\TestExecution;

class AdminDashboard extends Component
{
    public $filterPeriod = 'all'; // all, this_month, last_month, this_year
    public $filterTester = 'all';
    public $filterProject = 'all';

    public function render()
    {
        // Appliquer les filtres de base sur les projets
        $projectsQuery = Project::query();

        if ($this->filterProject !== 'all') {
            $projectsQuery->where('id', $this->filterProject);
        }

        if ($this->filterPeriod !== 'all') {
            $now = now();
            if ($this->filterPeriod === 'this_month') {
                $projectsQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($this->filterPeriod === 'last_month') {
                $projectsQuery->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->year);
            } elseif ($this->filterPeriod === 'this_year') {
                $projectsQuery->whereYear('created_at', $now->year);
            }
        }

        // On clone la requête pour les KPIs
        $activeProjectsCount = (clone $projectsQuery)->whereNotIn('status', ['completed', 'archived'])->count();
        $uatProjectsCount = (clone $projectsQuery)->where('status', 'in_progress')->count();

        // Si on filtre par testeur, on cherche les tests (TestCase) assignés
        $testCasesQuery = TestCase::whereHas('project', function($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        });
        if ($this->filterProject !== 'all') {
            $testCasesQuery->where('project_id', $this->filterProject);
        }
        if ($this->filterTester !== 'all') {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $this->filterTester)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $this->filterTester)->whereNotNull('template_id')->pluck('template_id')->toArray();
            
            $testCasesQuery->where(function($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                  ->orWhereIn('template_id', $assignedTemplateIds);
            });
        }
        $totalTemplates = $testCasesQuery->count();

        // Calcul du taux de validation en lisant l'état des TestCase directement
        $allTestCasesQuery = (clone $testCasesQuery)->get();
        $adminStats = \App\Models\TestCase::calculateStats($allTestCasesQuery);
        $totalExecutions = $adminStats['executed'];
        $validExecutions = $adminStats['valide'];
        $validationRate = $totalExecutions > 0 ? round(($validExecutions / $totalExecutions) * 100) : 0;

        // Projets pour la liste principale
        $mainProjects = (clone $projectsQuery)
            ->with(['client', 'developers', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        // Capacité de l'équipe (Testeurs)
        $testersCapacity = User::role('tester')->get()->map(function($tester) {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('template_id')->pluck('template_id')->toArray();
            
            $assignedCases = \App\Models\TestCase::whereHas('project', function($q) {
                    $q->whereNotIn('status', ['completed', 'archived']);
                })
                ->where(function($q) use ($assignedProjectIds, $assignedTemplateIds) {
                    $q->whereIn('project_id', $assignedProjectIds)
                      ->orWhereIn('template_id', $assignedTemplateIds);
                })
                ->get();
            $testerStats = \App\Models\TestCase::calculateStats($assignedCases);

            $total = $testerStats['total'];
            $done = $testerStats['executed'];

            $percent = $total > 0 ? round(($done / $total) * 100) : 100;
            return [
                'name' => $tester->name,
                'total' => $total,
                'done' => $done,
                'percent' => $percent,
                'status' => $percent >= 100 ? 'Disponible' : ($percent > 50 ? 'En cours' : 'Chargé'),
                'color' => $percent >= 100 ? 'green' : ($percent > 50 ? 'yellow' : 'red')
            ];
        });

        $unreadMessages = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->with('sender')
            ->latest()
            ->get();

        // Pour les filtres
        $allTesters = User::role('tester')->orderBy('name')->get();
        $allProjects = Project::orderBy('name')->get();

        return view('livewire.admin-dashboard', [
            'activeProjectsCount' => $activeProjectsCount,
            'uatProjectsCount' => $uatProjectsCount,
            'totalTemplates' => $totalTemplates,
            'validationRate' => $validationRate,
            'mainProjects' => $mainProjects,
            'testersCapacity' => $testersCapacity,
            'unreadMessages' => $unreadMessages,
            'allTesters' => $allTesters,
            'allProjects' => $allProjects,
        ]);
    }
}
