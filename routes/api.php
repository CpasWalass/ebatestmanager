<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;

Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * User Info
     */
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /**
     * Clients Routes
     */
    Route::apiResource('clients', ClientController::class)
        ->middleware('auth:sanctum');
    
    Route::get('/clients/{client}/projects', [ClientController::class, 'projects'])
        ->middleware('auth:sanctum');

    /**
     * Projects Routes
     */
    Route::apiResource('projects', ProjectController::class)
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/test-cases', [ProjectController::class, 'testCases'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/templates', [ProjectController::class, 'templates'])
        ->middleware('auth:sanctum');
    
    Route::get('/clients/{client}/projects-list', [ProjectController::class, 'byClient'])
        ->middleware('auth:sanctum');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
