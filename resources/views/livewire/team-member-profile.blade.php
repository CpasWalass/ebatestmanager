<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('equipe.index') }}" class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-900 dark:hover:text-white transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    Profil: {{ $user->name }}
                    @if(!$user->is_active)
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-md uppercase tracking-wider">Désactivé</span>
                    @endif
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
        </div>
        
        <button wire:click="toggleActiveStatus" class="px-4 py-2 {{ $user->is_active ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border border-green-200 hover:bg-green-100' }} rounded-lg text-sm font-medium transition shadow-sm">
            {{ $user->is_active ? 'Désactiver le compte' : 'Réactiver le compte' }}
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-xl border border-green-200 dark:border-green-800 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Projets assignés</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->testCaseAssignments->unique('project_id')->count() }}</p>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 text-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Templates en cours</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->testCaseAssignments->whereIn('status', ['pending', 'in_progress'])->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Assignments Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Projets & Templates en cours</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Projet</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Template</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($user->testCaseAssignments as $assignment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $assignment->project->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $assignment->template->name }}
                        </td>
                        <td class="px-6 py-4">
                            @if($assignment->status === 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Terminé</span>
                            @elseif($assignment->status === 'in_progress')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">En cours</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">En attente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('projets.cas-de-test', ['project' => $assignment->project_id, 'template' => $assignment->template_id]) }}" class="text-[#8b0000] hover:text-red-800 font-medium text-sm">
                                Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            Aucun projet assigné pour le moment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
