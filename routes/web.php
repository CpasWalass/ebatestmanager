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

    Route::get('/clients', function () {
        return view('clients.index');
    })->name('gestion.clients');
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
            return view('projets.show', compact('project'));
        })->name('projet.show');

        Route::get('/projets/{project}/cas-de-test/{template}', function (Project $project, TestCaseTemplate $template) {
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
