@extends('layouts.app')

@section('title', 'Espace Client — ' . tenant('name', 'Dashboard'))

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Espace Client</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Suivi de vos projets et rapports de recette (UAT)
            </p>
        </div>
        <a href="{{ route('client.test-cases') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-xl transition"
            style="background:#8b0000;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau Test UAT
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Projets Actifs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Projets Actifs</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $projets->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-50 dark:bg-gray-700 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Scénarios UAT --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vos Scénarios UAT</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalUat }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $uatValides }} validés</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Taux de Conformité --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Taux de Conformité</p>
                    <p class="text-3xl font-bold mt-1" style="color:{{ $conformite >= 80 ? '#16a34a' : ($conformite > 50 ? '#f59e0b' : '#dc2626') }}">{{ $conformite }}%</p>
                    <p class="text-xs text-gray-400 mt-1">Conformité aux attentes métier</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-green-50 dark:bg-green-900/20 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Rapports Disponibles --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Rapports Disponibles</h2>
        </div>

        @if($rapports->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun rapport publié</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Les rapports de recette apparaîtront ici lorsqu'ils seront prêts.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($rapports as $rapport)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $rapport->type === 'iat' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                {{ strtoupper($rapport->type) }}
                            </span>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $rapport->title }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Projet : <span class="font-medium">{{ $rapport->project?->name }}</span> · 
                            Publié le {{ $rapport->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <a href="#" class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                        Télécharger PDF
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
