<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;

class AdminReportController extends Controller
{
    public function generate(Request $request)
    {
        $filterPeriod = $request->input('period', 'all');
        $filterProject = $request->input('project', 'all');
        $filterTester = $request->input('tester', 'all');

        $projectsQuery = Project::query();
        if ($filterProject !== 'all') {
            $projectsQuery->where('id', $filterProject);
        }
        if ($filterPeriod !== 'all') {
            $now = now();
            if ($filterPeriod === 'this_month') {
                $projectsQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'last_month') {
                $projectsQuery->whereMonth('created_at', $now->subMonth()->month)->whereYear('created_at', $now->year);
            } elseif ($filterPeriod === 'this_year') {
                $projectsQuery->whereYear('created_at', $now->year);
            }
        }
        $activeProjectsCount = (clone $projectsQuery)->whereNotIn('status', ['completed', 'archived'])->count();
        $uatProjectsCount = (clone $projectsQuery)->where('status', 'in_progress')->count();

        // Tests assignés
        $testCasesQuery = TestCase::query();
        if ($filterProject !== 'all') {
            $testCasesQuery->where('project_id', $filterProject);
        }
        if ($filterTester !== 'all') {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $filterTester)->whereNotNull('template_id')->pluck('template_id')->toArray();
            
            $testCasesQuery->where(function($q) use ($assignedProjectIds, $assignedTemplateIds) {
                $q->whereIn('project_id', $assignedProjectIds)
                  ->orWhereIn('template_id', $assignedTemplateIds);
            });
        }

        $allTestCasesQuery = (clone $testCasesQuery)->get();
        $adminStats = \App\Models\TestCase::calculateStats($allTestCasesQuery);
        $totalExecutions = $adminStats['executed'];
        $validExecutions = $adminStats['valide'];
        $validationRate = $totalExecutions > 0 ? round(($validExecutions / $totalExecutions) * 100) : 0;
        $totalTemplates = $adminStats['total'];

        // Testeurs
        $testersCapacity = User::role('tester')->get()->map(function($tester) {
            $assignedProjectIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('project_id')->pluck('project_id')->toArray();
            $assignedTemplateIds = TestCaseAssignment::where('user_id', $tester->id)->whereNotNull('template_id')->pluck('template_id')->toArray();
            
            $assignedCases = \App\Models\TestCase::whereIn('project_id', $assignedProjectIds)->orWhereIn('template_id', $assignedTemplateIds)->get();
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

        $pdf = Pdf::loadView('reports.admin-global-pdf', compact(
            'activeProjectsCount',
            'uatProjectsCount',
            'totalTemplates',
            'validationRate',
            'testersCapacity',
            'filterPeriod',
            'filterProject',
            'filterTester'
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'sans-serif');

        $filename = 'rapport-admin-filtre-' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
