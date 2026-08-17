<?php

use App\Exports\ProjectExcelExport;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Developpeur\DeveloppeurDashboardController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\Testeur\ReportController;
use App\Http\Controllers\Testeur\TesteurDashboardController;
use App\Livewire\AdminDashboard;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\GlobalActivityLog;
use App\Livewire\TeamMemberProfile;
use App\Livewire\UserProfile;
use App\Livewire\UserSettings;
use App\Models\Client;
use App\Models\Message;
use App\Models\Project;
use App\Models\Report;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

/*
|--------------------------------------------------------------------------
| Page d'accueil publique
|--------------------------------------------------------------------------
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/
/*
|--------------------------------------------------------------------------
| Auth (login view personnalisée)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
});

/*
|--------------------------------------------------------------------------
| Routes partagées — projets (lecture pour Dev/Client, complet pour Chef)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/projets', function () {
        return view('projets.index');
    })->name('projets.index');

    Route::get('/projets/{project}', function (Project $project) {
        $user = auth()->user();

        // Vérification d'accès pour le client
        if ($user && $user->hasRole('client')) {
            $hasAccess = TestCaseAssignment::where('user_id', $user->id)
                ->where('project_id', $project->id)
                ->exists();
            if (! $hasAccess) {
                abort(403, "Vous n'avez pas accès à ce projet.");
            }
        }

        return view('projets.show', compact('project'));
    })->name('projets.show');

    Route::get('/projets/{project}/cas-de-test/{template}', function (Project $project, TestCaseTemplate $template) {
        return view('projets.test-editor', compact('project', 'template'));
    })->name('test-cases.show');

    // Export Excel des résultats d'un projet
    Route::get('/projets/{project}/export', function (Project $project) {
        $export = new ProjectExcelExport($project);
        $path = $export->export();

        return response()->download(storage_path('app/'.$path))->deleteFileAfterSend(true);
    })->name('projets.export');

    // Export PDF ou Word d'un rapport
    Route::get('/rapports/{report}/export', function (Request $request, Report $report) {
        $format = $request->input('format', 'pdf');
        $slug = Str::slug($report->perimeter ?? $report->title ?? 'rapport');
        if ($format === 'word') {
            $phpWord = new PhpWord;
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(11);
            $section = $phpWord->addSection();
            $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 18, 'color' => 'CC0000'], ['spaceAfter' => 200]);
            $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 13, 'color' => 'CC0000'], ['spaceBefore' => 200, 'spaceAfter' => 100]);
            $header = $section->addHeader();
            $header->addText('e-Business Afrique - EbaTestManager', ['size' => 9, 'color' => '888888']);
            $section->addTitle(strtoupper($report->perimeter ?? $report->title ?? 'Rapport'), 1);
            $section->addText('Projet : '.($report->project->name ?? '-'), ['size' => 11, 'color' => '555555']);
            $section->addText('Date : '.$report->created_at->format('d/m/Y'), ['size' => 10, 'italic' => true, 'color' => '888888']);
            $section->addTextBreak(1);
            if ($report->findings) {
                $section->addTitle('Constatations', 2);
                foreach ((is_array($report->findings) ? $report->findings : [$report->findings]) as $finding) {
                    $section->addText((string) $finding, ['size' => 10]);
                }
                $section->addTextBreak(1);
            }
            if ($report->description) {
                $section->addTitle('Description', 2);
                $section->addText($report->description, ['size' => 10]);
            }
            $footer = $section->addFooter();
            $footer->addText('Genere le '.now()->format('d/m/Y').' - EbaTestManager by e-Business Afrique', ['size' => 9, 'color' => '888888']);
            $tempPath = storage_path('app/temp_rapport_'.$slug.'_'.time().'.docx');
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempPath);

            return response()->download($tempPath, 'rapport_'.$slug.'.docx')->deleteFileAfterSend(true);
        }
        $pdf = Pdf::loadView('reports.pdf', compact('report'));

        return $pdf->download('rapport_'.$slug.'.pdf');
    })->name('rapports.export');

    // Rétro-compatibilité: ancien nom de route
    Route::get('/rapports/{report}/pdf', function (Request $request, Report $report) {
        $pdf = Pdf::loadView('reports.pdf', compact('report'));

        return $pdf->download('rapport_'.Str::slug($report->perimeter ?? $report->title).'.pdf');
    })->name('rapports.pdf');

    // Marquer un message comme lu
    Route::post('/messages/{message}/read', function (Message $message) {
        if ($message->receiver_id === auth()->id()) {
            $message->markAsRead();
        }

        return back();
    })->name('messages.read');

    // Profil & Paramètres
    Route::get('/profile', UserProfile::class)->name('profile.show');
    Route::get('/settings', UserSettings::class)->name('settings.show');

    // Journal global (Admin / Chef de projet)
    Route::get('/journal-global', GlobalActivityLog::class)->name('journal.global');

    // Upload d'image universel (commentaires, cellules de test, rejets client, etc.)
    Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');
});

/*
|--------------------------------------------------------------------------
| Routes Chef de Projet (actions exclusives)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified', 'role:chef_project'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, '__invoke'])->name('dashboard');
    Route::get('/admin/rapport-pdf', [AdminReportController::class, 'generate'])->name('admin.rapport-pdf');
    Route::get('/admin/rapport-statistiques-pdf', [AdminReportController::class, 'generateStatsPdf'])->name('admin.rapport-stats-pdf');

    Route::get('/equipe', function () {
        return view('users.index');
    })->name('equipe.index');

    Route::get('/equipe/{user}', TeamMemberProfile::class)->name('equipe.show');

    Route::get('/clients', function () {
        return view('clients.index');
    })->name('gestion.clients');

    Route::get('/clients/{client}', function (Client $client) {
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

        Route::get('/rapport-global', [ReportController::class, 'generateGlobalReport'])
            ->name('rapport-global');

        Route::get('/projets', function () {
            return view('projets.index');
        })->name('projets.index');

        Route::get('/projets/{project}', function (Project $project) {
            $user = auth()->user();
            if ($user->hasRole('developer')) {
                if (! $project->developers()->where('users.id', $user->id)->exists()) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }
            if ($user->hasRole('tester')) {
                $assignedToProject = TestCaseAssignment::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->exists();
                $assignedToTemplate = TestCaseAssignment::where('user_id', $user->id)
                    ->whereHas('template', function ($q) use ($project) {
                        $q->where('project_id', $project->id);
                    })
                    ->exists();
                if (! $assignedToProject && ! $assignedToTemplate) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }

            return view('projets.show', compact('project'));
        })->name('projets.show');

        Route::get('/projets/{project}/cas-de-test/{template}', function (Project $project, TestCaseTemplate $template) {
            $user = auth()->user();
            if ($user->hasRole('developer')) {
                if (! $project->developers()->where('users.id', $user->id)->exists()) {
                    abort(403, "Vous n'êtes pas assigné à ce projet.");
                }
            }
            if ($user->hasRole('tester')) {
                $assignedToProject = TestCaseAssignment::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->exists();
                $assignedToTemplate = TestCaseAssignment::where('user_id', $user->id)
                    ->where('template_id', $template->id)
                    ->exists();
                if (! $assignedToProject && ! $assignedToTemplate) {
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

    });
