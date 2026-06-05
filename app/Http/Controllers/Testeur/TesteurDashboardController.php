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
            ->withCount('testCases')
            ->get();

        // Stats globales du testeur
        $totalAssigned = TestCaseAssignment::where('user_id', $user->id)->count();
        $totalExecuted = TestExecution::where('tester_id', $user->id)->count();

        $executions = TestExecution::where('tester_id', $user->id)->get();
        $successCount = $executions->where('status', 'valide')->count();
        $failureCount = $executions->where('status', 'non_valide')->count();
        $reserveCount = $executions->where('status', 'sous_reserve')->count();
        $optimCount   = $executions->where('status', 'optimisation')->count();

        $successRate = $totalExecuted > 0
            ? round(($successCount / $totalExecuted) * 100, 1)
            : 0;

        return view('testeur.dashboard', compact(
            'assignedProjects',
            'totalAssigned',
            'totalExecuted',
            'successCount',
            'failureCount',
            'reserveCount',
            'optimCount',
            'successRate',
        ));
    }
}
