<?php

namespace App\Services;

use App\Models\TestCase;
use App\Models\TestExecution;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class TestExecutionService
{
    /**
     * Créer ou mettre à jour une exécution de test
     */
    public function upsertExecution(TestCase $testCase, User $tester, array $data): TestExecution
    {
        $execution = TestExecution::firstOrNew([
            'test_case_id' => $testCase->id,
            'tester_id'    => $tester->id,
        ]);

        if (!$execution->exists) {
            $execution->started_at = now();
            $execution->tenant_id  = $testCase->tenant_id;
        }

        $execution->fill([
            'results'  => $data['results']  ?? $execution->results,
            'status'   => $data['status']   ?? $execution->status ?? 'non_valide',
            'comments' => $data['comments'] ?? $execution->comments,
            'nature'   => $data['nature']   ?? $execution->nature,
            'priority' => $data['priority'] ?? $execution->priority,
        ]);

        if (isset($data['status']) && in_array($data['status'], ['valide', 'non_valide', 'sous_reserve', 'optimisation'])) {
            $execution->completed_at = now();
        }

        $execution->save();

        return $execution;
    }

    /**
     * Statistiques de progression pour un testeur sur un projet
     */
    public function getProgressStats(Project $project, User $tester): array
    {
        $assigned = $project->testCases()
            ->whereHas('assignments', fn($q) => $q->where('user_id', $tester->id))
            ->pluck('id');

        $executions = TestExecution::where('tester_id', $tester->id)
            ->whereIn('test_case_id', $assigned)
            ->get();

        $total    = $assigned->count();
        $done     = $executions->count();
        $success  = $executions->where('status', 'valide')->count();
        $failure  = $executions->where('status', 'non_valide')->count();
        $reserve  = $executions->where('status', 'sous_reserve')->count();
        $optim    = $executions->where('status', 'optimisation')->count();
        $progress = $total > 0 ? round(($done / $total) * 100) : 0;

        return compact('total', 'done', 'success', 'failure', 'reserve', 'optim', 'progress');
    }

    /**
     * Récupère les exécutions formatées pour affichage
     */
    public function getFormattedExecutions(Project $project, User $tester): Collection
    {
        return $project->testCases()
            ->whereHas('assignments', fn($q) => $q->where('user_id', $tester->id))
            ->with(['template', 'executions' => fn($q) => $q->where('tester_id', $tester->id)])
            ->get()
            ->map(function ($tc) {
                $exec = $tc->executions->first();
                return [
                    'id'       => $tc->id,
                    'data'     => $tc->data,
                    'template' => $tc->template,
                    'status'   => $exec?->status ?? 'non_commence',
                    'execution'=> $exec,
                ];
            });
    }
}
