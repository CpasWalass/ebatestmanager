<?php

namespace App\Http\Controllers\Developpeur;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\View\View;

class DeveloppeurDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Rapports envoyés au développeur (status sent)
        $rapportsRecus = Report::where('status', 'sent')
            ->with(['project.client', 'creator'])
            ->latest()
            ->get();

        // Mes réponses
        $mesReponses = ReportResponse::where('user_id', $user->id)
            ->with('report.project')
            ->latest()
            ->take(5)
            ->get();

        $projetsEnRevue = Project::where('status', 'in_review')
            ->with('client')
            ->latest()
            ->get();

        $enAttente  = $rapportsRecus->count() + $projetsEnRevue->count();
        $traites    = ReportResponse::where('user_id', $user->id)
            ->where('status', 'done')->count();

        return view('developpeur.dashboard', compact(
            'rapportsRecus',
            'mesReponses',
            'projetsEnRevue',
            'enAttente',
            'traites',
        ));
    }
}
