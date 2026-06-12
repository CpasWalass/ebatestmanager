@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Fil d'Ariane --}}
    <nav class="flex mb-6 text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('gestion.clients') }}" class="hover:text-gray-900 dark:hover:text-white transition">Clients</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-gray-900 dark:text-white">{{ $client->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Header client --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                style="background: linear-gradient(135deg, #8b0000, #cc0000);">
                {{ strtoupper(substr($client->name, 0, 2)) }}
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h1>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($client->email)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $client->email }}
                    </span>
                    @endif
                    @if($client->phone)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $client->phone }}
                    </span>
                    @endif
                    @if($client->city || $client->country)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ implode(', ', array_filter([$client->city, $client->country])) }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                    {{ $client->projects->count() }} projet(s)
                </span>
            </div>
        </div>
    </div>

    {{-- Projets du client --}}
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Projets associés</h2>
        <a href="{{ route('projets.index') }}" class="px-4 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-md font-medium text-sm flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau projet
        </a>
    </div>

    @if($client->projects->isEmpty())
    <div class="py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
        </svg>
        <p class="mt-3 text-sm text-gray-400">Aucun projet pour ce client.</p>
        <a href="{{ route('projets.index') }}" class="mt-4 inline-block text-sm text-[#8b0000] hover:underline font-medium">Créer un projet →</a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($client->projects as $project)
        <a href="{{ route('projets.show', $project) }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 p-6 transition group cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-[10px] font-bold rounded-md uppercase tracking-wider mb-3">
                        {{ $project->type ?? 'Web' }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#8b0000] transition-colors line-clamp-1">{{ $project->name }}</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $project->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                       ($project->status === 'in_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                    {{ $project->status === 'active' ? 'Actif' : ($project->status === 'in_review' ? 'En révision' : 'Brouillon') }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 line-clamp-2 min-h-[40px]">
                {{ $project->description ?: 'Aucune description.' }}
            </p>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-xs font-medium text-gray-500">{{ $project->testCases->count() }} tests</span>
                </div>
                <span class="text-xs font-medium text-gray-400">{{ $project->created_at->format('d/m/Y') }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
