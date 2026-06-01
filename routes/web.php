<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users Management Views
    Route::view('/users', 'users.index')->name('users.index');
    
    // Templates Management Views
    Route::view('/templates', 'templates.index')->name('templates.index');
    
    // Test Cases Management Views
    Route::view('/test-cases', 'test-cases.index')->name('test-cases.index');
});
