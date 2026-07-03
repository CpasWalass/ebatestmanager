<div>
    <div class="space-y-8">
        <!-- Page Header & Filters -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
            
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="filterPeriod" class="px-4 py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Toutes les périodes</option>
                    <option value="this_month">Ce mois-ci</option>
                    <option value="last_month">Le mois dernier</option>
                    <option value="this_year">Cette année</option>
                </select>

                <select wire:model.live="filterProject" class="px-4 py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tous les projets</option>
                    @foreach($allProjects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTester" class="px-4 py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tous les testeurs</option>
                    @foreach($allTesters as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <div wire:loading class="text-sm text-gray-500">
                    <svg class="animate-spin h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card: Projets Actifs -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Projets Actifs</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $activeProjectsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card: Phase UAT/In progress -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">En Phase Test (UAT)</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $uatProjectsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card: Tests Assignés -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Tests Assignés</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $totalTemplates }}</p>
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
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $validationRate }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages / Notifications -->
        @if($unreadMessages->count() > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 shadow-sm mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-blue-900 dark:text-blue-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Nouveaux retours développeurs ({{ $unreadMessages->count() }})
                </h2>
            </div>
            <div class="space-y-3">
                @foreach($unreadMessages as $message)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm border border-blue-100 dark:border-blue-700">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->sender->name }}</span>
                        <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $message->content }}</p>
                    <div class="mt-3 text-right">
                        <form action="{{ route('messages.read', $message->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800">Marquer comme lu</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Active Projects List -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Projets filtrés</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">NOM DU PROJET</th>
                                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold hidden md:table-cell">CLIENT</th>
                                    <th class="px-6 py-3 text-left text-gray-700 dark:text-gray-300 font-semibold">PHASE</th>
                                    <th class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-semibold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mainProjects as $project)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Créé le {{ $project->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 hidden md:table-cell">
                                        {{ $project->client->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeClass = match($project->status) {
                                                'completed' => 'bg-green-100 text-green-700',
                                                'in_progress' => 'bg-blue-100 text-blue-700',
                                                'in_review' => 'bg-yellow-100 text-yellow-700',
                                                'archived' => 'bg-gray-100 text-gray-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $badgeClass }}">
                                            {{ str_replace('_', ' ', $project->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('projets.show', $project->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Aucun projet trouvé pour ces filtres.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Team Capacity -->
            <div class="bg-gray-800 dark:bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-white font-bold text-lg">Capacité Équipe (Testeurs)</h3>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Rapport Global
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            class="absolute right-0 top-full mt-1 w-44 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                            <a href="{{ route('admin.rapport-pdf', ['period' => $filterPeriod, 'project' => $filterProject, 'tester' => $filterTester, 'format' => 'pdf']) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Exporter en PDF
                            </a>
                            <a href="{{ route('admin.rapport-pdf', ['period' => $filterPeriod, 'project' => $filterProject, 'tester' => $filterTester, 'format' => 'word']) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 transition border-t border-gray-100 dark:border-gray-600">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Exporter en Word
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @forelse($testersCapacity as $tester)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm text-gray-300">{{ $tester['name'] }}</p>
                            <span class="text-xs font-bold px-2 py-1 rounded
                                {{ $tester['color'] === 'green' ? 'text-green-400 bg-green-900/30' : ($tester['color'] === 'yellow' ? 'text-yellow-400 bg-yellow-900/30' : 'text-red-400 bg-red-900/30') }}">
                                {{ $tester['status'] }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Exécutés: {{ $tester['done'] }}</span>
                            <span>Assignés: {{ $tester['total'] }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $tester['color'] === 'green' ? 'bg-green-500' : ($tester['color'] === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $tester['percent'] }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Aucun testeur trouvé.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
