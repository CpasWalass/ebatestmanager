<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Testeur\TesteurDashboardController;
use App\Http\Controllers\Developpeur\DeveloppeurDashboardController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Models\Project;
use App\Models\TestCaseTemplate;

/*
|--------------------------------------------------------------------------
| Page d'accueil publique
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Auth (login view personnalisée)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
});

/*
|--------------------------------------------------------------------------
| Routes partagées — projets (lecture pour Dev/Client, complet pour Chef)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Les projets en lecture seule — tous les rôles peuvent y accéder
    Route::get('/projets', function () {
        return view('projets.index');
    })->name('projets.index');

    Route::get('/projets/{project}', function (Project $project) {
        return view('projets.show', compact('project'));
    })->name('projets.show');

    Route::get('/projets/{project}/cas-de-test/{template}', function (Project $project, TestCaseTemplate $template) {
        return view('projets.test-editor', compact('project', 'template'));
    })->name('test-cases.show');

    // Export Excel des résultats d'un projet
    Route::get('/projets/{project}/export', function (Project $project) {
        $export = new \App\Exports\ProjectExcelExport($project);
        $path = $export->export();
        return response()->download(storage_path('app/' . $path))->deleteFileAfterSend(true);
    })->name('projets.export');

    // Export PDF d'un rapport
    Route::get('/rapports/{report}/pdf', function (\App\Models\Report $report) {
        // Optionnel : vérifier les droits d'accès
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('report'));
        return $pdf->download('rapport_' . \Illuminate\Support\Str::slug($report->perimeter ?? $report->title) . '.pdf');
    })->name('rapports.pdf');

    // Marquer un message comme lu
    Route::post('/messages/{message}/read', function (\App\Models\Message $message) {
        if ($message->receiver_id === auth()->id()) {
            $message->markAsRead();
        }
        return back();
    })->name('messages.read');

    // Profil & Paramètres
    Route::get('/profile', \App\Livewire\UserProfile::class)->name('profile.show');
    Route::get('/settings', \App\Livewire\UserSettings::class)->name('settings.show');
    
    // Journal global (Admin / Chef de projet)
    Route::get('/journal-global', \App\Livewire\GlobalActivityLog::class)->name('journal.global');
});

/*
|--------------------------------------------------------------------------
| Routes Chef de Projet (actions exclusives)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified', 'role:chef_project'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/equipe', function () {
        return view('users.index');
    })->name('equipe.index');

    Route::get('/equipe/{user}', \App\Livewire\TeamMemberProfile::class)->name('equipe.show');

    Route::get('/clients', function () {
        return view('clients.index');
    })->name('gestion.clients');

    Route::get('/clients/{client}', function (\App\Models\Client $client) {
        return view('clients.show', compact('client'));
    })->name('gestion.client.show');
});

/*
|--------------------------------------------------------------------------
| Routes Testeur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified', 'role:tester'])
    ->prefix('testeur')
    ->name('testeur.')
    ->group(function () {

        Route::get('/dashboard', [TesteurDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/projets', function () {
            return view('projets.index');
        })->name('projets.index');

        Route::get('/projets/{project}', function (Project $project) {
            $user = auth()->user();
            if ($user->hasRole('developer')) {
                if (!$project->developers()->where('users.id', $user->id)->exists()) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }
            if ($user->hasRole('tester')) {
                $assignedToProject = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->exists();
                $assignedToTemplate = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                    ->whereHas('template', function($q) use ($project) {
                        $q->where('project_id', $project->id);
                    })
                    ->exists();
                if (!$assignedToProject && !$assignedToTemplate) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }
            return view('projets.show', compact('project'));
        })->name('projets.show');

        Route::get('/projets/{project}/cas-de-test/{template}', function (Project $project, TestCaseTemplate $template) {
            $user = auth()->user();
            if ($user->hasRole('developer')) {
                if (!$project->developers()->where('users.id', $user->id)->exists()) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }
            if ($user->hasRole('tester')) {
                $assignedToProject = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->exists();
                $assignedToTemplate = \App\Models\TestCaseAssignment::where('user_id', $user->id)
                    ->where('template_id', $template->id)
                    ->exists();
                if (!$assignedToProject && !$assignedToTemplate) {
                    abort(403, "Vous n'êtes pas assigné à ce cas de test.");
                }
            }
            return view('projets.test-editor', compact('project', 'template'));
        })->name('executer');
    });

/*
|--------------------------------------------------------------------------
| Routes Développeur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified', 'role:developer'])
    ->prefix('developpeur')
    ->name('developpeur.')
    ->group(function () {
        Route::get('/dashboard', [DeveloppeurDashboardController::class, 'index'])
            ->name('dashboard');
        
        Route::post('/rapports/reply', [DeveloppeurDashboardController::class, 'reply'])
            ->name('rapports.reply');
    });

/*
|--------------------------------------------------------------------------
| Routes Client
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/test-cases', function () {
            return view('client.test-cases');
        })->name('test-cases');
    });
