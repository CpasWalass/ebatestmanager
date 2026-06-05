@extends('layouts.app')

@section('title', 'Projet — Testeur')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('testeur.dashboard') }}" class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Projet : {{ $project->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Cas de test assignés
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @php
                $testCases = $project->testCases()
                    ->whereHas('assignments', fn($q) => $q->where('user_id', auth()->id()))
                    ->with(['executions' => fn($q) => $q->where('tester_id', auth()->id())])
                    ->get();
            @endphp
            
            @if($testCases->isEmpty())
                <div class="p-8 text-center text-gray-500">Aucun cas de test assigné sur ce projet.</div>
            @endif

            @foreach($testCases as $tc)
                @php 
                    $exec = $tc->executions->first(); 
                    $status = $exec->status ?? 'non_commence';
                    
                    $statusColor = match($status) {
                        'valide' => 'text-green-600 bg-green-50',
                        'non_valide' => 'text-red-600 bg-red-50',
                        'sous_reserve' => 'text-amber-600 bg-amber-50',
                        'optimisation' => 'text-blue-600 bg-blue-50',
                        default => 'text-gray-600 bg-gray-50',
                    };
                    $statusLabel = match($status) {
                        'valide' => 'Validé',
                        'non_valide' => 'Échec',
                        'sous_reserve' => 'Sous réserve',
                        'optimisation' => 'Optimisation',
                        default => 'Non commencé',
                    };
                @endphp
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $tc->data['cas_test'] ?? 'Cas de Test #' . $tc->id }}</h3>
                        <p class="text-sm text-gray-500">{{ $tc->data['modules'] ?? '' }} - {{ $tc->data['fonctionnalites'] ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                        <a href="{{ route('testeur.executer', [$project->id, $tc->id]) }}" class="px-4 py-2 text-sm font-medium text-white rounded-xl transition" style="background:#CC0000; hover:background:#aa0000;">
                            {{ $status === 'non_commence' ? 'Commencer' : 'Modifier' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
