<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = tenant('id') ?? $request->user()->tenant_id;

        $user = $request->user();

        // Récupérer les projets auxquels le client est assigné
        $assignedProjectIds = TestCaseAssignment::where('user_id', $user->id)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->toArray();

        // Projets assignés
        $projets = Project::whereIn('id', $assignedProjectIds)
            ->where(function ($query) {
                $query->where('type', 'uat')
                    ->orWhereHas('testCases', function ($q) {
                        $q->where('type', 'uat');
                    });
            })
            ->with(['testCases' => function ($q) {
                $q->where('type', 'uat');
            }])
            ->get();

        // Récupérer les rapports (seulement ceux finalisés pour le client)
        $rapports = Report::where('tenant_id', $tenantId)
            ->whereIn('status', ['sent', 'acknowledged'])
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        // Statistiques Client strictes (restreintes aux projets assignés)
        $clientTestCases = TestCase::where('tenant_id', $tenantId)
            ->whereIn('project_id', $assignedProjectIds)
            ->where('type', 'uat')
            ->get();

        $clientStats = TestCase::clientStatsFor($clientTestCases);

        $uatValides = $clientStats['validated'];
        $totalUat = $clientStats['total'];

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
