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

        // Si on filtre par testeur, on cherche les templates assignés à ce testeur
        $templatesQuery = TestCaseTemplate::query();
        if ($this->filterTester !== 'all') {
            $templatesQuery->whereHas('assignments', function($q) {
                $q->where('user_id', $this->filterTester);
            });
        }
        $totalTemplates = $templatesQuery->count();

        // Calcul du taux de validation
        // On récupère les exécutions. Si un projet ou un testeur est filtré, on filtre les exécutions.
        $executionsQuery = TestExecution::query();
        if ($this->filterTester !== 'all') {
            $executionsQuery->where('tester_id', $this->filterTester);
        }
        if ($this->filterProject !== 'all') {
            $executionsQuery->whereHas('testCase', function($q) {
                $q->where('project_id', $this->filterProject);
            });
        }
        if ($this->filterPeriod !== 'all') {
            $now = now();
            if ($this->filterPeriod === 'this_month') {
                $executionsQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($this->filterPeriod === 'last_month') {
                $executionsQuery->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->year);
            } elseif ($this->filterPeriod === 'this_year') {
                $executionsQuery->whereYear('created_at', $now->year);
            }
        }

        $totalExecutions = $executionsQuery->count();
        $validExecutions = (clone $executionsQuery)->where('status', 'valide')->count();
        $validationRate = $totalExecutions > 0 ? round(($validExecutions / $totalExecutions) * 100) : 0;

        // Projets pour la liste principale
        $mainProjects = (clone $projectsQuery)
            ->with(['client', 'developers', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        // Capacité de l'équipe (Testeurs)
        $testersCapacity = User::role('tester')->withCount([
            'testCaseAssignments as assigned_count',
            'testExecutions as executed_count'
        ])->get()->map(function($tester) {
            $total = $tester->assigned_count;
            $done = $tester->executed_count;
            // On considère que si assigné > 0, charge = (restant / total) * 100
            // Mais plus simplement, on peut juste afficher le ratio
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
