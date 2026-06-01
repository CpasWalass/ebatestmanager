@extends('layouts.app')

@section('title', 'Test Cases - EbaTestManager')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
    <span class="text-gray-400">/</span>
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Projets</a>
    <span class="text-gray-400">/</span>
    <span class="text-gray-900 dark:text-white font-medium">Test Cases</span>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold">Test Cases</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Gestion des cas de test pour le projet Portail Services</p>
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Ajouter Test Case</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" placeholder="Rechercher par CAS..." class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                <select class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les Statuts</option>
                    <option value="validé">Validé</option>
                    <option value="non-validé">Non Validé</option>
                    <option value="sous-reserve">Sous Réserve</option>
                </select>
                <select class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les Testeurs</option>
                    <option value="ahmed">Ahmed B.</option>
                    <option value="fatima">Fatima K.</option>
                </select>
                <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-md font-medium text-sm">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Test Cases List -->
        <div class="space-y-4">
            <!-- Test Case Item -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-lg font-bold">TC-001: Connexion Utilisateur Standard</h3>
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-medium rounded">Validé</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Vérifier que l'authentification fonctionne avec credentials valides</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Assigné à</p>
                        <div class="flex -space-x-2 justify-end">
                            <div class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs font-bold border-2 border-white dark:border-gray-800">A</div>
                        </div>
                    </div>
                </div>

                <!-- Fields Preview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Module</p>
                        <p class="font-medium">Authentification</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Fonctionnalité</p>
                        <p class="font-medium">Login Portal</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Nature</p>
                        <p class="font-medium">Fonctionnel</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Date Prévue</p>
                        <p class="font-medium">10/01/2024</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                        Voir Détails
                    </button>
                    <button class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Modifier
                    </button>
                    <button class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Test Case Item 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-lg font-bold">TC-002: Connexion Utilisateur Admin</h3>
                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 text-xs font-medium rounded">Sous Réserve</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Vérifier les permissions admin après connexion</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Assigné à</p>
                        <div class="flex -space-x-2 justify-end">
                            <div class="w-8 h-8 rounded-full bg-pink-500 text-white flex items-center justify-center text-xs font-bold border-2 border-white dark:border-gray-800">F</div>
                        </div>
                    </div>
                </div>

                <!-- Fields Preview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Module</p>
                        <p class="font-medium">Authentification</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Fonctionnalité</p>
                        <p class="font-medium">Admin Console</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Nature</p>
                        <p class="font-medium">Sécurité</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Date Prévue</p>
                        <p class="font-medium">12/01/2024</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                        Voir Détails
                    </button>
                    <button class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Modifier
                    </button>
                    <button class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-md text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">Affichage 1 à 2 de 47 résultats</p>
            <div class="flex space-x-2">
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Précédent</button>
                <button class="px-3 py-2 rounded-md bg-blue-600 text-white text-sm">1</button>
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">2</button>
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">3</button>
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Suivant</button>
            </div>
        </div>
    </div>
@endsection
