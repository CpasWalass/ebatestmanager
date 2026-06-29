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
        $assignedCases = \App\Models\TestCase::whereIn('project_id', $assignedProjectIds)
            ->orWhereIn('template_id', $assignedTemplateIds)
            ->get();
            
        $stats = \App\Models\TestCase::calculateStats($assignedCases);

        $totalAssigned = $stats['total'];
        $totalExecuted = $stats['executed'];
        
        $successCount = $stats['valide'];
        $failureCount = $stats['non_valide'];
        $reserveCount = $stats['sous_reserve'];
        $optimCount   = $stats['optimisation'];

        $successRate = $totalExecuted > 0 ? round(($successCount / $totalExecuted) * 100, 1) : 0;

        // 2. Répartition par Cas de Test (Template)
        // Récupère tous les templates que le testeur doit tester
        $templates = \App\Models\TestCaseTemplate::whereIn('project_id', $assignedProjectIds)
            ->orWhereIn('id', $assignedTemplateIds)
            ->with(['project'])
            ->get();
            
        $templatesProgress = [];
        foreach ($templates as $template) {
            $templateCases = \App\Models\TestCase::where('template_id', $template->id)->get();
            $testCount = $templateCases->count();
            if ($testCount > 0) {
                // Nombre de tests validés par CE testeur sur ce template (en réalité, juste validés sur ce template)
                $templateStats = \App\Models\TestCase::calculateStats($templateCases);
                $validCount = $templateStats['valide'];
                    
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
