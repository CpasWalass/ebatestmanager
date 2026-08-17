<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Livewire\Component;

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
        $closedProjectsCount = (clone $projectsQuery)->where('status', 'completed')->count();
        $uatProjectsCount = (clone $projectsQuery)->where('type', 'uat')->whereNotIn('status', ['completed', 'archived'])->count();

        // Si on filtre par testeur, on cherche les tests (TestCase) assignés
        $testCasesQuery = TestCase::whereHas('project', function ($q) {
            $q->whereNotIn('status', ['completed', 'archived']);
        });
        if ($this->filterProject !== 'all') {
            $testCasesQuery->where('project_id', $this->filterProject);
        }
        if ($this->filterTester !== 'all') {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $this->filterTester)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $this->filterTester)->whereNotNull('template_id')->pluck('template_id')->toArray();

            $testCasesQuery->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                    ->orWhereIn('template_id', $assignedTemplateIds);
            });
        }
        $totalTemplates = $testCasesQuery->count();

        // Calcul du taux de validation Interne (IAT)
        $allTestCasesQuery = (clone $testCasesQuery)->with(['project', 'verdictBy'])->get();
        $adminStats = TestCase::statsFor($allTestCasesQuery);
        $authorBreakdown = TestCase::statsByAuthorBreakdown($allTestCasesQuery);
        $totalExecutions = $adminStats['executed'];
        $validExecutions = $adminStats['valide'];
        $validationRate = $totalExecutions > 0 ? round(($validExecutions / $totalExecutions) * 100) : 0;

        // Répartition par projet (respecte les filtres actifs)
        $perProjectStats = $allTestCasesQuery->groupBy('project_id')->map(function ($cases) {
            $stats = TestCase::statsFor($cases);
            $project = $cases->first()->project;

            return [
                'name' => $project?->name ?? ('Projet #'.$cases->first()->project_id),
                'total' => $stats['total'],
                'executed' => $stats['executed'],
                'valide' => $stats['valide'],
                'non_valide' => $stats['non_valide'],
                'sous_reserve' => $stats['sous_reserve'],
                'optimisation' => $stats['optimisation'],
                'bloque' => $stats['bloque'],
                'non_executed' => $stats['total'] - $stats['executed'],
                'taux_execution' => $stats['taux_execution'],
            ];
        })->sortByDesc('total')->values();

        // Calcul du taux d'approbation Client (UAT)
        $uatTestCases = (clone $testCasesQuery)->where('type', 'uat')->get();
        $clientStats = TestCase::clientStatsFor($uatTestCases);
        $totalUat = $clientStats['total'];
        $validUat = $clientStats['validated'];
        $clientValidationRate = $totalUat > 0 ? round(($validUat / $totalUat) * 100) : 0;

        // Projets pour la liste principale
        $mainProjects = (clone $projectsQuery)
            ->with(['client', 'developers', 'createdBy'])
            ->latest()
            ->take(5)
            ->get();

        // Capacité de l'équipe (Testeurs)
        $testersCapacity = User::role('tester')->get()->map(function ($tester) {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('template_id')->pluck('template_id')->toArray();

            $assignedCases = TestCase::whereHas('project', function ($q) {
                $q->whereNotIn('status', ['completed', 'archived']);
            })
                ->where(function ($q) use ($assignedProjectIds, $assignedTemplateIds) {
                    $q->whereIn('project_id', $assignedProjectIds)
                        ->orWhereIn('template_id', $assignedTemplateIds);
                })
                ->get();
            $testerStats = TestCase::statsFor($assignedCases);

            $total = $testerStats['total'];
            $done = $testerStats['executed'];

            $percent = $total > 0 ? round(($done / $total) * 100) : 100;

            return [
                'name' => $tester->name,
                'total' => $total,
                'done' => $done,
                'percent' => $percent,
                'status' => $percent >= 100 ? 'Disponible' : ($percent > 50 ? 'En cours' : 'Chargé'),
                'color' => $percent >= 100 ? 'green' : ($percent > 50 ? 'yellow' : 'red'),
            ];
        });

        // Pour les filtres
        $allTesters = User::role('tester')->orderBy('name')->get();
        $allProjects = Project::orderBy('name')->get();

        return view('livewire.admin-dashboard', [
            'activeProjectsCount' => $activeProjectsCount,
            'closedProjectsCount' => $closedProjectsCount,
            'uatProjectsCount' => $uatProjectsCount,
            'totalTemplates' => $totalTemplates,
            'validationRate' => $validationRate,
            'clientValidationRate' => $clientValidationRate,
            'mainProjects' => $mainProjects,
            'testersCapacity' => $testersCapacity,
            'adminStats' => $adminStats,
            'authorBreakdown' => $authorBreakdown,
            'clientStats' => $clientStats,
            'perProjectStats' => $perProjectStats,
            'allTesters' => $allTesters,
            'allProjects' => $allProjects,
        ]);
    }
}
