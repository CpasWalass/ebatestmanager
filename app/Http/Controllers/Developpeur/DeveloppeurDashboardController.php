<?php

namespace App\Http\Controllers\Developpeur;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeveloppeurDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Rapports envoyés au développeur (status sent) des projets qui lui sont assignés
        $rapportsRecus = Report::where('status', 'sent')
            ->whereHas('project.developers', function ($query) use ($user) {
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
            ->whereHas('developers', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with('client')
            ->latest()
            ->get();

        $enAttente = $rapportsRecus->count();
        $traites = ReportResponse::where('user_id', $user->id)
            ->where('status', 'done')->count();

        return view('developpeur.dashboard', compact(
            'rapportsRecus',
            'mesReponses',
            'projetsEnRevue',
            'enAttente',
            'traites',
        ));
    }

    public function reply(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'content' => 'nullable|string',
        ], [
            'report_id.required' => 'Le rapport est introuvable.',
        ]);

        $content = $request->content ?: 'Correction effectuée sans commentaire additionnel.';

        $response = ReportResponse::create([
            'report_id' => $request->report_id,
            'user_id' => auth()->id(),
            'content' => $content,
            'status' => 'done',
        ]);

        // Marquer le rapport comme répondu
        $report = Report::find($request->report_id);
        $report->update(['status' => 'resolved']);

        // Si tous les rapports du projet sont résolus, on repasse le projet en actif
        if ($report->project) {
            $remainingReports = Report::where('project_id', $report->project_id)
                ->where('status', 'sent')
                ->count();

            if ($remainingReports === 0) {
                $report->project->update(['status' => 'in_progress']);
            }
        }

        // Notifier le chef de projet
        if ($report->project && $report->project->createdBy) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $report->project->created_by,
                'project_id' => $report->project_id,
                'type' => 'system',
                'content' => 'Le développeur '.auth()->user()->name." a répondu au rapport {$report->perimeter} :\n\n\"{$content}\"",
            ]);
        }

        return redirect()->back()->with('success', 'Votre réponse a été envoyée avec succès.');
    }
}
