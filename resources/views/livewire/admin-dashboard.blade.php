<div>
    <div class="space-y-8">

        {{-- ── Page Header & Filters ── --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>

            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="filterPeriod"
                    class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-800">
                    <option value="all">Toutes les périodes</option>
                    <option value="this_month">Ce mois-ci</option>
                    <option value="last_month">Le mois dernier</option>
                    <option value="this_year">Cette année</option>
                </select>

                <select wire:model.live="filterProject"
                    class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-800">
                    <option value="all">Tous les projets</option>
                    @foreach($allProjects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTester"
                    class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-800">
                    <option value="all">Tous les testeurs</option>
                    @foreach($allTesters as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <div wire:loading class="text-sm text-gray-500">
                    <svg class="animate-spin h-5 w-5 inline text-red-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── KPI Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Projets Actifs --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Projets Actifs</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $activeProjectsCount }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(139,0,0,0.08);">
                        <svg class="w-6 h-6" style="color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="trend text-green-600 dark:text-green-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        Projets en cours
                    </span>
                </div>
            </div>

            {{-- En Phase UAT --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">En Phase Test (UAT)</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $uatProjectsCount }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="trend text-gray-400">Phase de test active</span>
                </div>
            </div>

            {{-- Tests Assignés --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Tests Assignés</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $totalTemplates }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Total des cas de test</p>
                    </div>
                    {{-- Anneau de progression --}}
                    <div class="ring-wrap">
                        @php
                            $circum = 150.8;
                            $ringPct = min(100, max(0, $validationRate));
                            $offset = $circum - ($ringPct / 100 * $circum);
                        @endphp
                        <svg viewBox="0 0 56 56">
                            <circle class="ring-track" cx="28" cy="28" r="24" fill="none" stroke-width="4"/>
                            <circle cx="28" cy="28" r="24" fill="none" stroke="#2563eb" stroke-width="4"
                                stroke-linecap="round" stroke-dasharray="{{ $circum }}"
                                stroke-dashoffset="{{ $offset }}"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="trend text-blue-600 dark:text-blue-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        Cas de test assignés
                    </span>
                </div>
            </div>

            {{-- Taux de Validation --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Taux de Validation</p>
                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ $validationRate }}%</p>
                    </div>
                    {{-- Anneau de progression --}}
                    <div class="ring-wrap">
                        @php
                            $valOffset = $circum - ($validationRate / 100 * $circum);
                            $valColor = $validationRate >= 80 ? '#16a34a' : ($validationRate >= 50 ? '#d97706' : '#dc2626');
                        @endphp
                        <svg viewBox="0 0 56 56">
                            <circle class="ring-track" cx="28" cy="28" r="24" fill="none" stroke-width="4"/>
                            <circle cx="28" cy="28" r="24" fill="none" stroke="{{ $valColor }}" stroke-width="4"
                                stroke-linecap="round" stroke-dasharray="{{ $circum }}"
                                stroke-dashoffset="{{ $valOffset }}"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-5 h-5" style="color:{{ $valColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="trend" style="color:{{ $validationRate >= 50 ? '#16a34a' : '#dc2626' }};">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        Taux validation
                    </span>
                </div>
            </div>

        </div>

        {{-- ── Messages / Notifications ── --}}
        @if($unreadMessages->count() > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-blue-900 dark:text-blue-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Nouveaux messages ({{ $unreadMessages->count() }})
                </h2>
            </div>
            <div class="space-y-3">
                @foreach($unreadMessages as $message)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-blue-100 dark:border-blue-700">
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

        {{-- ── Main Content Grid ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Projets Filtrés --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Projets filtrés</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">NOM DU PROJET</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">CLIENT</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PHASE</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mainProjects as $project)
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $project->name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Créé le {{ $project->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                        {{ $project->client->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeStyle = match($project->status) {
                                                'completed'  => 'background:rgba(22,163,74,0.1);color:#15803d;border:1px solid rgba(22,163,74,0.25);',
                                                'in_progress'=> 'background:rgba(37,99,235,0.1);color:#1d4ed8;border:1px solid rgba(37,99,235,0.25);',
                                                'in_review'  => 'background:rgba(217,119,6,0.1);color:#b45309;border:1px solid rgba(217,119,6,0.25);',
                                                'archived'   => 'background:rgba(107,114,128,0.1);color:#4b5563;border:1px solid rgba(107,114,128,0.25);',
                                                default      => 'background:rgba(107,114,128,0.1);color:#4b5563;border:1px solid rgba(107,114,128,0.25);',
                                            };
                                            $badgeStyleDark = match($project->status) {
                                                'completed'  => 'background:rgba(22,163,74,0.2);color:#4ade80;border:1px solid rgba(22,163,74,0.3);',
                                                'in_progress'=> 'background:rgba(37,99,235,0.2);color:#60a5fa;border:1px solid rgba(37,99,235,0.3);',
                                                'in_review'  => 'background:rgba(217,119,6,0.2);color:#fbbf24;border:1px solid rgba(217,119,6,0.3);',
                                                'archived'   => 'background:rgba(107,114,128,0.2);color:#9ca3af;border:1px solid rgba(107,114,128,0.3);',
                                                default      => 'background:rgba(107,114,128,0.2);color:#9ca3af;border:1px solid rgba(107,114,128,0.3);',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="{{ $badgeStyle }}">
                                            {{ str_replace('_', ' ', $project->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('projets.show', $project->id) }}"
                                           class="text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                                           style="color:#8b0000; background:rgba(139,0,0,0.07);"
                                           onmouseover="this.style.background='rgba(139,0,0,0.15)'"
                                           onmouseout="this.style.background='rgba(139,0,0,0.07)'">
                                            Voir →
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        Aucun projet trouvé pour ces filtres.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Capacité Équipe (Testeurs) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-gray-900 dark:text-white font-bold text-lg">Capacité Équipe</h3>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition"
                            style="background:#8b0000;"
                            onmouseover="this.style.background='#a10000'"
                            onmouseout="this.style.background='#8b0000'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Rapport
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                            class="absolute right-0 top-full mt-1 w-44 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                            <a href="{{ route('admin.rapport-pdf', ['period' => $filterPeriod, 'project' => $filterProject, 'tester' => $filterTester, 'format' => 'pdf']) }}"
                               target="_blank"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4" style="color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
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

                @php
                    $overloaded  = collect($testersCapacity)->where('color', 'red')->count();
                    $available   = collect($testersCapacity)->where('color', 'green')->count();
                    $warning     = collect($testersCapacity)->where('color', 'yellow')->count();
                @endphp
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">
                    {{ count($testersCapacity) }} testeur(s)
                    @if($overloaded > 0) · <span class="font-semibold overload-flag">{{ $overloaded }} surchargé(s)</span>@endif
                    @if($available > 0) · <span class="font-semibold text-green-600 dark:text-green-400">{{ $available }} disponible(s)</span>@endif
                </p>

                <div class="space-y-5">
                    @forelse($testersCapacity as $tester)
                    @php
                        // Couleurs selon la charge
                        $ringColor  = match($tester['color']) {
                            'green'  => '#16a34a',
                            'yellow' => '#d97706',
                            'red'    => '#c23b3b',
                            default  => '#6b7280',
                        };
                        $badgeText  = match($tester['color']) {
                            'green'  => 'Disponible',
                            'yellow' => 'Chargé',
                            'red'    => 'Surchargé',
                            default  => $tester['status'],
                        };
                        $badgeCss = match($tester['color']) {
                            'green'  => 'color:#15803d;background:rgba(22,163,74,0.1);',
                            'yellow' => 'color:#b45309;background:rgba(217,119,6,0.1);',
                            'red'    => 'color:#b91c1c;background:rgba(185,28,28,0.1);',
                            default  => 'color:#6b7280;background:rgba(107,114,128,0.1);',
                        };
                        $badgeCssDark = match($tester['color']) {
                            'green'  => 'color:#4ade80;background:rgba(22,163,74,0.2);',
                            'yellow' => 'color:#fbbf24;background:rgba(217,119,6,0.2);',
                            'red'    => 'color:#ff9d9d;background:rgba(139,0,0,0.35);',
                            default  => 'color:#9ca3af;background:rgba(107,114,128,0.2);',
                        };
                        $circum = 106.8;
                        $ringOffset = $circum - ($tester['percent'] / 100 * $circum);
                        $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $tester['name']), 0, 2))));
                    @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-2.5">
                            {{-- Avatar ring --}}
                            <div class="avatar-ring">
                                <svg viewBox="0 0 40 40">
                                    <circle class="avatar-track" cx="20" cy="20" r="17" fill="none" stroke-width="3"/>
                                    <circle cx="20" cy="20" r="17" fill="none"
                                        stroke="{{ $ringColor }}" stroke-width="3" stroke-linecap="round"
                                        stroke-dasharray="{{ $circum }}"
                                        stroke-dashoffset="{{ $ringOffset }}"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-900 dark:text-white">
                                    {{ $initials }}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold truncate">{{ $tester['name'] }}</p>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0" style="{{ $badgeCss }}">
                                        {{ $badgeText }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($tester['total'] > 0)
                                        {{ $tester['total'] }} tests assignés
                                    @else
                                        Aucun test assigné
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($tester['total'] > 0)
                        <div class="pl-[52px] space-y-1.5">
                            <div class="flex justify-between text-[11px] text-gray-400">
                                <span>Progression</span>
                                <span>{{ $tester['done'] }}/{{ $tester['total'] }} exécutés ({{ $tester['percent'] }}%)</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                     style="width:{{ $tester['percent'] }}%; background:{{ $ringColor }};"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Aucun testeur trouvé.</p>
                    @endforelse
                </div>

            </div>

        </div>
    </div>
</div>
