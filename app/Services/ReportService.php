<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Project;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Génère un rapport depuis les exécutions d'un projet
     */
    public function generateReport(Project $project, User $creator, array $meta = []): Report
    {
        $executions = \App\Models\TestExecution::whereHas('testCase', fn($q) => $q->where('project_id', $project->id))
            ->get();

        $total   = $executions->count();
        $success = $executions->where('status', 'valide')->count();
        $failure = $executions->where('status', 'non_valide')->count();
        $reserve = $executions->where('status', 'sous_reserve')->count();
        $optim   = $executions->where('status', 'optimisation')->count();

        $stats = compact('total', 'success', 'failure', 'reserve', 'optim');

        $report = Report::create([
            'project_id'     => $project->id,
            'created_by'     => $creator->id,
            'title'          => 'Rapport ' . $project->name . ' — ' . now()->format('d/m/Y'),
            'type'           => $meta['type']            ?? 'iat',
            'tested_version' => $meta['tested_version']  ?? null,
            'test_date'      => $meta['test_date']        ?? now()->toDateString(),
            'responsible'    => $meta['responsible']      ?? $creator->name,
            'perimeter'      => $meta['perimeter']        ?? null,
            'stats'          => $stats,
            'notes'          => $meta['notes']            ?? null,
            'status'         => 'draft',
            'tenant_id'      => $project->tenant_id,
        ]);

        return $report;
    }

    /**
     * Exporte le rapport en PDF
     */
    public function exportToPdf(Report $report): \Illuminate\Http\Response
    {
        $report->load(['project.client', 'creator']);

        $pdf = Pdf::loadView('reports.pdf-template', compact('report'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'sans-serif');

        $filename = 'rapport-' . \Str::slug($report->title) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Calcule les statistiques globales pour l'affichage du rapport
     */
    public function getReportStats(Report $report): array
    {
        $stats = $report->stats ?? [];
        $total = $stats['total'] ?? 0;

        return [
            'total'       => $total,
            'success'     => $stats['success']     ?? 0,
            'failure'     => $stats['failure']      ?? 0,
            'reserve'     => $stats['reserve']      ?? 0,
            'optim'       => $stats['optim']        ?? 0,
            'success_pct' => $total > 0 ? round((($stats['success'] ?? 0) / $total) * 100, 1) : 0,
        ];
    }
}
