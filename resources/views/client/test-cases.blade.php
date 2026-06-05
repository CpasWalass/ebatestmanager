@extends('layouts.app')

@section('title', 'Vos Tests UAT — ' . tenant('name', 'Espace Client'))

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.dashboard') }}" class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Vos Scénarios UAT</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Proposez et suivez l'exécution de vos scénarios de test métier
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Formulaire (colonne gauche) --}}
        <div class="lg:col-span-1">
            @livewire('client.create-uat-test-case')
        </div>

        {{-- Liste des scénarios (colonne droite) --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Scénarios Soumis</h2>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php
                        $tenantId = tenant('id') ?? auth()->user()->tenant_id;
                        $testCases = \App\Models\TestCase::where('tenant_id', $tenantId)
                            ->where('type', 'uat')
                            ->with('project', 'executions')
                            ->latest()
                            ->get();
                    @endphp

                    @if($testCases->isEmpty())
                        <div class="p-8 text-center text-gray-500">
                            Aucun scénario UAT soumis pour le moment. Utilisez le formulaire pour ajouter votre premier test.
                        </div>
                    @else
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
                                    default => 'En attente d\'exécution',
                                };
                            @endphp
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">{{ $tc->project->name ?? 'Général' }}</span>
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $tc->data['cas_test'] ?? 'Test UAT #' . $tc->id }}</h3>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $tc->data['scenarios_test'] ?? 'Aucune description' }}</p>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
