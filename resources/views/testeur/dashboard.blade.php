@extends('layouts.app')

@section('title', 'Tableau de Bord — Testeur')
@section('search_placeholder', 'Rechercher un cas de test...')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Bienvenue, <span class="font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</span> · Mise à jour en temps réel
            </p>
        </div>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-xl transition"
            style="background:#CC0000;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exporter le Rapport
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Cas Assignés --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cas Assignés</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalAssigned }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total à traiter</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(204,0,0,0.1);">
                    <svg class="w-5 h-5" style="color:#CC0000" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Taux de Réussite --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Taux de Réussite</p>
                    <p class="text-3xl font-bold mt-1" style="color:#16a34a">{{ $successRate }}%</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $successCount }} validés / {{ $totalExecuted }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-green-50 dark:bg-green-900/20">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Échecs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anomalies</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $failureCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $reserveCount }} sous réserve</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 dark:bg-red-900/20">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Optimisations --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Optimisations</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $optimCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Améliorations suggérées</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-amber-50 dark:bg-amber-900/20">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne gauche : Projets assignés --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Mes Projets Assignés</h2>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ $assignedProjects->count() }} projet(s)
                    </span>
                </div>

                @if($assignedProjects->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun projet assigné</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Le chef de projet vous assignera des tests bientôt</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($assignedProjects as $project)
                            @php
                                $casCount = $project->testCases->count();
                                $executed = \App\Models\TestExecution::where('tester_id', auth()->id())
                                    ->whereIn('test_case_id', $project->testCases->pluck('id'))
                                    ->count();
                                $pct = $casCount > 0 ? round(($executed / $casCount) * 100) : 0;
                            @endphp
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0"
                                                style="background:{{ $pct >= 100 ? '#16a34a' : ($pct > 0 ? '#f59e0b' : '#CC0000') }}"></span>
                                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $project->name }}</h3>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                            {{ $project->client?->name ?? 'Client non défini' }} ·
                                            {{ $casCount }} cas de test assignés
                                        </p>
                                        {{-- Barre de progression --}}
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500"
                                                    style="width:{{ $pct }}%; background:{{ $pct >= 100 ? '#16a34a' : '#CC0000' }};"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 w-8 text-right">{{ $pct }}%</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('testeur.projet.show', $project->id) }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-lg border transition"
                                        style="color:#CC0000; border-color:#CC0000;"
                                        onmouseover="this.style.background='#CC0000';this.style.color='white';"
                                        onmouseout="this.style.background='transparent';this.style.color='#CC0000';">
                                        {{ $pct >= 100 ? 'Voir' : 'Reprendre' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Colonne droite : Résumé & Activité --}}
        <div class="space-y-4">
            {{-- Résumé des statuts --}}
            <div class="bg-gray-900 dark:bg-gray-950 rounded-2xl p-5 text-white">
                <h3 class="font-semibold text-white mb-4">Résumé des Tests</h3>
                <div class="space-y-3">
                    @php
                        $statsItems = [
                            ['label' => 'Validés',      'count' => $successCount, 'color' => '#16a34a', 'emoji' => '✅'],
                            ['label' => 'Échecs',       'count' => $failureCount, 'color' => '#CC0000',  'emoji' => '💣'],
                            ['label' => 'Sous réserve', 'count' => $reserveCount, 'color' => '#f59e0b', 'emoji' => '🤔'],
                            ['label' => 'Optimisation', 'count' => $optimCount,   'color' => '#3b82f6', 'emoji' => '👷'],
                        ];
                        $totalEx = max($totalExecuted, 1);
                    @endphp
                    @foreach($statsItems as $s)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-300">{{ $s['emoji'] }} {{ $s['label'] }}</span>
                                <span class="text-sm font-bold text-white">{{ $s['count'] }}</span>
                            </div>
                            <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ round(($s['count']/$totalEx)*100) }}%; background:{{ $s['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="w-full mt-5 py-2.5 text-sm font-semibold text-white rounded-xl transition"
                    style="background:#CC0000;"
                    onmouseover="this.style.background='#aa0000'"
                    onmouseout="this.style.background='#CC0000'">
                    Générer le Rapport
                </button>
            </div>

            {{-- Activité récente --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Activité Récente</h3>
                @php
                    $recentExec = \App\Models\TestExecution::where('tester_id', auth()->id())
                        ->with('testCase.project')
                        ->latest()
                        ->take(4)
                        ->get();
                @endphp
                @if($recentExec->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">Aucune activité récente</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentExec as $exec)
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 text-base leading-none">
                                    {{ match($exec->status) {
                                        'valide' => '✅',
                                        'non_valide' => '💣',
                                        'sous_reserve' => '🤔',
                                        'optimisation' => '👷',
                                        default => '⏳'
                                    } }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                        {{ $exec->testCase?->project?->name ?? 'Projet' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $exec->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('fab')
<a href="#"
    class="fixed bottom-24 right-6 w-13 h-13 flex items-center justify-center rounded-2xl text-white shadow-lg transition hover:scale-110 active:scale-95 z-50"
    style="background:#CC0000; width:52px; height:52px; box-shadow: 0 4px 20px rgba(204,0,0,0.4);">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
</a>
@endsection
