@extends('layouts.app')

@section('title', 'Templates de Test - EbaTestManager')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
    <span class="text-gray-400">/</span>
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Projets</a>
    <span class="text-gray-400">/</span>
    <span class="text-gray-900 dark:text-white font-medium">Templates</span>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold">Templates de Test Cases</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Gestion des modèles de test case pour le projet</p>
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Créer Template</span>
            </button>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Template Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold">Template Portail Services</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Template Info -->
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Template IAT pour tests d'intégration applicatifs</p>

                <!-- Field Count -->
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                    <p class="text-sm"><span class="font-bold text-blue-600 dark:text-blue-400">11 Champs</span> définis</p>
                </div>

                <!-- Field List Preview -->
                <div class="space-y-2 mb-4">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Champs :</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">CAS Test</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">État</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">Résultat</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">+8 autres</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button class="flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                        Éditer
                    </button>
                    <button class="flex-1 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Add Template Card (Empty State) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 flex flex-col items-center justify-center hover:border-blue-500 transition cursor-pointer">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Ajouter un Template</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Cliquez pour créer</p>
            </div>
        </div>
    </div>
@endsection


