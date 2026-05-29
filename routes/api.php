<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TestCaseTemplateController;
use App\Http\Controllers\TestCaseController;

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

    /**
     * Test Case Templates Routes
     */
    Route::get('/projects/{project}/templates', [TestCaseTemplateController::class, 'index'])
        ->middleware('auth:sanctum');
    
    Route::post('/projects/{project}/templates', [TestCaseTemplateController::class, 'store'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/templates/default-fields', [TestCaseTemplateController::class, 'defaultFields'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/templates/{template}', [TestCaseTemplateController::class, 'show'])
        ->middleware('auth:sanctum');
    
    Route::put('/projects/{project}/templates/{template}', [TestCaseTemplateController::class, 'update'])
        ->middleware('auth:sanctum');
    
    Route::delete('/projects/{project}/templates/{template}', [TestCaseTemplateController::class, 'destroy'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/templates/{template}/field-options/{fieldName}', [TestCaseTemplateController::class, 'fieldOptions'])
        ->middleware('auth:sanctum');

    /**
     * Test Cases Routes
     */
    Route::get('/projects/{project}/test-cases', [TestCaseController::class, 'index'])
        ->middleware('auth:sanctum');
    
    Route::post('/projects/{project}/templates/{template}/test-cases', [TestCaseController::class, 'store'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/test-cases/{testCase}', [TestCaseController::class, 'show'])
        ->middleware('auth:sanctum');
    
    Route::put('/projects/{project}/test-cases/{testCase}', [TestCaseController::class, 'update'])
        ->middleware('auth:sanctum');
    
    Route::delete('/projects/{project}/test-cases/{testCase}', [TestCaseController::class, 'destroy'])
        ->middleware('auth:sanctum');
    
    Route::get('/projects/{project}/templates/{template}/test-cases', [TestCaseController::class, 'byTemplate'])
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
