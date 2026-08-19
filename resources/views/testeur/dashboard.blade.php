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
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Cas Assignés --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tests Assignés</p>
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
            
            @if(isset($rapportsEnRetest) && $rapportsEnRetest->count() > 0)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-amber-900 dark:text-amber-100 text-lg">Corrections à re-tester</h2>
                            <p class="text-sm text-amber-700 dark:text-amber-300">Le chef de projet a renvoyé ces rapports pour vérification suite aux corrections.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($rapportsEnRetest as $report)
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-amber-100 dark:border-amber-900/50 flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $report->project->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Périmètre: {{ $report->perimeter }}</p>
                                </div>
                                <a href="{{ route('testeur.projets.show', $report->project_id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                    Aller re-tester
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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
                                $projectStats = \App\Models\TestCase::statsFor($project->testCases);
                                $executed = $projectStats['executed'];
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
                                            {{ $casCount }} tests assignés
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
                                    <a href="{{ route('testeur.projets.show', $project->id) }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-lg border transition"
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
                            ['label' => 'Validés',      'count' => $successCount, 'color' => '#16a34a'],
                            ['label' => 'Échecs',       'count' => $failureCount, 'color' => '#CC0000'],
                            ['label' => 'Sous réserve', 'count' => $reserveCount, 'color' => '#f59e0b'],
                            ['label' => 'Optimisation', 'count' => $optimCount,   'color' => '#3b82f6'],
                        ];
                        $totalEx = max($totalExecuted, 1);
                    @endphp
                    @foreach($statsItems as $s)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $s['color'] }}"></span>
                                    <span class="text-sm text-gray-300">{{ $s['label'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-white">{{ $s['count'] }}</span>
                            </div>
                            <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ round(($s['count']/$totalEx)*100) }}%; background:{{ $s['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Dropdown choix format rapport --}}
                <div class="relative mt-5" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white rounded-xl transition"
                        style="background:#CC0000;"
                        onmouseover="this.style.background='#aa0000'"
                        onmouseout="this.style.background='#CC0000'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Générer le Rapport
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute bottom-full mb-1 left-0 right-0 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                        <a href="{{ route('testeur.rapport-global', ['format' => 'pdf']) }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Exporter en PDF
                        </a>
                        <a href="{{ route('testeur.rapport-global', ['format' => 'word']) }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 transition border-t border-gray-100 dark:border-gray-600">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Exporter en Word (.docx)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Activité récente --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Activité Récente</h3>
                @php
                    $recentExec = \App\Models\TestExecution::where('tester_id', auth()->id())
                        ->where('created_at', '>=', now()->subDays(7))
                        ->with('testCase.project')
                        ->latest('created_at')
                        ->take(4)
                        ->get();
                @endphp
                @if($recentExec->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">Aucune activité récente</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentExec as $exec)
                            <div class="flex items-start gap-3">
                                <span class="mt-1 flex-shrink-0">
                                    @php
                                        $color = match($exec->status) {
                                            'valide' => '#16a34a',
                                            'non_valide' => '#CC0000',
                                            'sous_reserve' => '#f59e0b',
                                            'optimisation' => '#3b82f6',
                                            default => '#9ca3af'
                                        };
                                    @endphp
                                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:{{ $color }}"></span>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                        {{ $exec->testCase?->project?->name ?? 'Projet' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $exec->created_at->diffForHumans() }}</p>
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

