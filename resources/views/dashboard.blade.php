@extends('layouts.app')

@section('title', 'Dashboard - EbaTestManager')

@section('breadcrumb')
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
    <span class="text-gray-400">/</span>
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Projets</a>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold">Liste des Projets</h1>
            <div class="flex items-center space-x-3">
                <select class="px-4 py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Tous les Clients</option>
                </select>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Filtres d'urgence</span>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card: Projets Actifs -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Projets Actifs</p>
                        <p class="text-3xl font-bold mt-2">24</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-green-600 dark:text-green-400">↑ 3 créés</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card: Phase UAT -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">En Phase UAT</p>
                        <p class="text-3xl font-bold mt-2">12</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-red-600 dark:text-red-400">3 critiques</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card: Templates Assignés -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Templates Assignés</p>
                        <p class="text-3xl font-bold mt-2">86</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-blue-600 dark:text-blue-400">57% disponible</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card: Taux de Validation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Taux de Validation</p>
                        <p class="text-3xl font-bold mt-2">78%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-green-600 dark:text-green-400">↑ 4% la last week</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Active Project -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Project Header -->
                    <div class="bg-gradient-to-r from-red-50 dark:from-red-900/10 to-red-50 dark:to-red-900/5 p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold rounded-full uppercase">URGENT</span>
                                <h3 class="text-xl font-bold mt-3">Portail Services BUBEDRA</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span class="font-medium">Client:</span> <span class="text-red-600 dark:text-red-400 font-semibold">BUBEDRA</span>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-purple-400 flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-gray-800">A</div>
                                    <div class="w-8 h-8 rounded-full bg-blue-400 flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-gray-800">B</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details -->
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Statut Actuel</p>
                                <p class="text-lg font-semibold text-red-600">Phase UAT</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Dernière Mise à jour</p>
                                <p class="text-lg font-semibold">6 y a 2 heures</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Progression</p>
                                <p class="text-lg font-semibold">85%</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Progrès général</span>
                                <span class="font-semibold">85%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-red-600 h-3 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-3 pt-4">
                            <button class="flex-1 px-4 py-2 flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Assigner des Testeurs</span>
                            </button>
                            <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-md font-medium text-sm transition">
                                Voir le Dossier
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Team Capacity -->
            <div class="bg-gray-800 dark:bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                <h3 class="text-white font-bold text-lg mb-4">Capacité Équipe</h3>
                
                <!-- Capacity Items -->
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm text-gray-300">IMT Backend</p>
                            <span class="text-xs font-bold text-red-400 bg-red-900/30 px-2 py-1 rounded">Chargé (85%)</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm text-gray-300">UAT Frontend</p>
                            <span class="text-xs font-bold text-green-400 bg-green-900/30 px-2 py-1 rounded">Disponible (60%)</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <button class="w-full mt-6 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition">
                    Optimiser l'Assignation
                </button>
            </div>
        </div>

        <!-- Autres Projets En Cours -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold">Autres Projets En Cours</h3>
                <a href="#" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">Voir Tout</a>
            </div>

            <!-- Projects Table (Responsive) -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">NOM DU PROJET</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold hidden md:table-cell">CLIENT</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold hidden lg:table-cell">EQUIPE</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">PHASE</th>
                            <th class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-semibold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium">Migration Core Banking</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 md:hidden">Version 1.0.0</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 hidden md:table-cell">SOCIÉTÉ GÉNÉRALE</td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="flex -space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-400 text-white text-xs flex items-center justify-center border border-gray-200 dark:border-gray-700">C</div>
                                    <div class="w-6 h-6 rounded-full bg-pink-400 text-white text-xs flex items-center justify-center border border-gray-200 dark:border-gray-700">D</div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-medium rounded">Phase IAT</span></td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
