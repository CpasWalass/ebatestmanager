<?php

namespace App\Http\Controllers\Testeur;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestExecution;
use Illuminate\View\View;

class TesteurDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Projets auxquels le testeur est assigné (soit projet complet, soit via un template)
        $assignedProjectIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();
            
        $assignedTemplateProjectIds = \App\Models\TestCaseTemplate::whereIn('id', function($q) use ($user) {
            $q->select('template_id')
              ->from('test_case_assignments')
              ->where('user_id', $user->id)
              ->whereNotNull('template_id');
        })->pluck('project_id')->toArray();
        
        $allAssignedProjectIds = array_unique(array_merge($assignedProjectIds, $assignedTemplateProjectIds));

        $assignedProjects = Project::whereIn('id', $allAssignedProjectIds)
            ->whereNotIn('status', ['completed', 'archived'])
            ->withCount('testCases')
            ->get();

        $assignedTemplateIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('template_id')
            ->pluck('template_id')
            ->toArray();

        // Stats globales du testeur (comptabiliser les tests individuels, pas les assignations de groupe)
        $totalAssigned = \App\Models\TestCase::whereIn('project_id', $assignedProjectIds)
            ->orWhereIn('template_id', $assignedTemplateIds)
            ->count();
            
        $totalExecuted = TestExecution::where('tester_id', $user->id)->count();

        $executions = TestExecution::where('tester_id', $user->id)->get();
        $successCount = $executions->where('status', 'valide')->count();
        $failureCount = $executions->where('status', 'non_valide')->count();
        $reserveCount = $executions->where('status', 'sous_reserve')->count();
        $optimCount   = $executions->where('status', 'optimisation')->count();

        $successRate = $totalExecuted > 0
            ? round(($successCount / $totalExecuted) * 100, 1)
            : 0;

        // Rapports en re-test (à vérifier par le testeur suite à une correction)
        $rapportsEnRetest = \App\Models\Report::where('status', 'retest')
            ->whereIn('project_id', $allAssignedProjectIds)
            ->with(['project', 'creator'])
            ->latest()
            ->get();

        return view('testeur.dashboard', compact(
            'assignedProjects',
            'totalAssigned',
            'totalExecuted',
            'successCount',
            'failureCount',
            'reserveCount',
            'optimCount',
            'successRate',
            'rapportsEnRetest',
        ));
    }
}
