<?php

namespace App\Http\Controllers\Testeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TestExecution;
use App\Models\TestCaseAssignment;

class ReportController extends Controller
{
    public function generateGlobalReport(Request $request)
    {
        $user = auth()->user();

        // Projets et templates assignés
        $assignedProjectIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();
            
        $assignedTemplateIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('template_id')
            ->pluck('template_id')
            ->toArray();
            
        $assignedTemplateProjectIds = \App\Models\TestCaseTemplate::whereIn('id', $assignedTemplateIds)
            ->pluck('project_id')
            ->toArray();
            
        $allAssignedProjectIds = array_unique(array_merge($assignedProjectIds, $assignedTemplateProjectIds));

        // 1. Statistiques globales (en tests)
        $totalAssigned = \App\Models\TestCase::whereIn('project_id', $assignedProjectIds)
            ->orWhereIn('template_id', $assignedTemplateIds)
            ->count();
            
        $totalExecuted = TestExecution::where('tester_id', $user->id)->count();
        
        $executions = TestExecution::where('tester_id', $user->id)->get();
        $successCount = $executions->where('status', 'valide')->count();
        $failureCount = $executions->where('status', 'non_valide')->count();
        $reserveCount = $executions->where('status', 'sous_reserve')->count();
        $optimCount   = $executions->where('status', 'optimisation')->count();

        $successRate = $totalExecuted > 0 ? round(($successCount / $totalExecuted) * 100, 1) : 0;

        // 2. Répartition par Cas de Test (Template)
        // Récupère tous les templates que le testeur doit tester
        $templates = \App\Models\TestCaseTemplate::whereIn('project_id', $assignedProjectIds)
            ->orWhereIn('id', $assignedTemplateIds)
            ->with(['project'])
            ->get();
            
        $templatesProgress = [];
        foreach ($templates as $template) {
            $testCount = \App\Models\TestCase::where('template_id', $template->id)->count();
            if ($testCount > 0) {
                // Nombre de tests validés par CE testeur sur ce template
                $validCount = TestExecution::where('tester_id', $user->id)
                    ->whereHas('testCase', function($q) use ($template) {
                        $q->where('template_id', $template->id);
                    })
                    ->where('status', 'valide')
                    ->count();
                    
                $templatesProgress[] = [
                    'project' => $template->project->name ?? '—',
                    'name' => $template->name,
                    'assigned' => $testCount,
                    'validated' => $validCount,
                    'percent' => round(($validCount / $testCount) * 100)
                ];
            }
        }

        $pdf = Pdf::loadView('reports.testeur-global-pdf', compact(
            'user',
            'totalAssigned',
            'totalExecuted',
            'successCount',
            'failureCount',
            'reserveCount',
            'optimCount',
            'successRate',
            'templatesProgress'
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'sans-serif');

        $filename = 'rapport-global-testeur-' . \Str::slug($user->name) . '-' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
