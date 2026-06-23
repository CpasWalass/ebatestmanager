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

        // Rapports envoyés au développeur (status sent) des projets qui lui sont assignés
        $rapportsRecus = Report::where('status', 'sent')
            ->whereHas('project.developers', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
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
            ->whereHas('developers', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with('client')
            ->latest()
            ->get();

        $enAttente  = $rapportsRecus->count();
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

    public function reply(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'content' => 'required|string|min:3',
        ]);

        $response = ReportResponse::create([
            'report_id' => $request->report_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'status' => 'done',
        ]);

        // Marquer le rapport comme répondu
        $report = Report::find($request->report_id);
        $report->update(['status' => 'resolved']);

        // Notifier le chef de projet
        if ($report->project && $report->project->createdBy) {
            \App\Models\Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $report->project->created_by,
                'project_id' => $report->project_id,
                'type' => 'system',
                'content' => "Le développeur **" . auth()->user()->name . "** a répondu au rapport **{$report->perimeter}** :\n\n\"{$request->content}\"",
            ]);
        }

        return redirect()->back()->with('success', 'Votre réponse a été envoyée avec succès.');
    }
}
