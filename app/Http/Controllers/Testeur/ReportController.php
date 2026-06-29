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

        // 1. Statistiques globales
        $totalAssigned = TestCaseAssignment::where('user_id', $user->id)->count();
        $totalExecuted = TestExecution::where('tester_id', $user->id)->count();
        
        $executions = TestExecution::where('tester_id', $user->id)->get();
        $successCount = $executions->where('status', 'valide')->count();
        $failureCount = $executions->where('status', 'non_valide')->count();
        $reserveCount = $executions->where('status', 'sous_reserve')->count();
        $optimCount   = $executions->where('status', 'optimisation')->count();

        $successRate = $totalExecuted > 0 ? round(($successCount / $totalExecuted) * 100, 1) : 0;

        // 2. Résumé des derniers tests
        $recentTests = TestExecution::where('tester_id', $user->id)
            ->with(['testCase.project'])
            ->latest()
            ->take(20)
            ->get();

        $pdf = Pdf::loadView('reports.testeur-global-pdf', compact(
            'user',
            'totalAssigned',
            'totalExecuted',
            'successCount',
            'failureCount',
            'reserveCount',
            'optimCount',
            'successRate',
            'recentTests'
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'sans-serif');

        $filename = 'rapport-global-testeur-' . \Str::slug($user->name) . '-' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
