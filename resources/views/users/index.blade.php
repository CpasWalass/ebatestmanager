@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - EbaTestManager')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
    <span class="text-gray-400">/</span>
    <span class="text-gray-900 dark:text-white font-medium">Utilisateurs</span>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold">Gestion des Utilisateurs</h1>
            <button @click="addUserModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Ajouter Utilisateur</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" placeholder="Rechercher par nom ou email..." class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                <select class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les Rôles</option>
                    <option value="chef_project">Chef de Projet</option>
                    <option value="tester">Testeur</option>
                    <option value="developer">Developer</option>
                    <option value="client">Client</option>
                </select>
                <select class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les Statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">NOM</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">EMAIL</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold hidden md:table-cell">RÔLE</th>
                            <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold hidden lg:table-cell">CRÉE LE</th>
                            <th class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-semibold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold">A</div>
                                    <p class="font-medium">Ahmed Bouhaddouz</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">chef@ebatest.local</td>
                            <td class="px-6 py-4 hidden md:table-cell"><span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-medium rounded">Chef de Projet</span></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 hidden lg:table-cell">06/01/2024</td>
                            <td class="px-6 py-4 text-right flex justify-end items-center space-x-2">
                                <button class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">Affichage 1 à 10 de 24 résultats</p>
            <div class="flex space-x-2">
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Précédent</button>
                <button class="px-3 py-2 rounded-md bg-blue-600 text-white text-sm">1</button>
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">2</button>
                <button class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Suivant</button>
            </div>
        </div>
    </div>
@endsection
