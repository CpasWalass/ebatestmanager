<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = tenant('id') ?? $request->user()->tenant_id;

        $user = $request->user();

        // Récupérer les projets auxquels le client est assigné
        $assignedProjectIds = \App\Models\TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();

        $projets = Project::whereIn('id', $assignedProjectIds)
            ->with('testCases')
            ->get();

        // Récupérer les rapports (seulement ceux finalisés pour le client)
        $rapports = Report::where('tenant_id', $tenantId)
            ->whereIn('status', ['sent', 'acknowledged'])
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        // Nombre de cas de test UAT validés (succès)
        $uatValides = TestCase::where('tenant_id', $tenantId)
            ->where('type', 'uat')
            ->whereHas('executions', function($q) {
                $q->where('status', 'valide');
            })->count();

        // Total UAT
        $totalUat = TestCase::where('tenant_id', $tenantId)->where('type', 'uat')->count();
        
        $conformite = $totalUat > 0 ? round(($uatValides / $totalUat) * 100) : 0;

        return view('client.dashboard', compact(
            'projets',
            'rapports',
            'uatValides',
            'totalUat',
            'conformite'
        ));
    }
}
