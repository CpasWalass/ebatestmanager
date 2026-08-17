


<div>
    <!-- Fil d'Ariane -->
    <nav class="flex mb-4 text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                @php
                    $backRoute = (auth()->check() && auth()->user()->hasRole('tester'))
                        ? route('testeur.projets.index')
                        : route('projets.index');
                @endphp
                <a href="{{ $backRoute }}" class="hover:text-gray-900 dark:hover:text-white transition">Projets</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-gray-900 dark:text-white">{{ $project->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold">Cas de Tests</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Dossiers de tests pour le projet <span class="font-semibold text-[#8b0000]">{{ $project->name }}</span></p>
        </div>
        
        @if(auth()->check() && auth()->user()->hasRole('chef_project'))
        <div class="flex items-center gap-2" x-data="{ openActions:false }">
            <!-- toolbar: actions fréquentes, style discret et groupé -->
            <div class="flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-1 shadow-sm">
                <label for="globalExcelUpload" class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition cursor-pointer relative" title="Importer un classeur Excel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span class="hidden sm:inline">Import</span>
                    <input type="file" id="globalExcelUpload" wire:model.live="globalExcelFile" accept=".xlsx,.xls,.csv" class="hidden">
                    <div wire:loading wire:target="globalExcelFile" class="absolute inset-0 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-lg">
                        <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </label>
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                <a href="{{ route('projets.export', $project->id) }}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="Exporter les résultats">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span class="hidden sm:inline">Export</span>
                </a>
            </div>

            <!-- menu Actions : regroupe tout ce qui dépend de la phase du projet -->
            <div class="relative">
                <button @click="openActions=!openActions" class="flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm shadow-sm transition">
                    Actions
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openActions" x-cloak @click.outside="openActions=false"
                    x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50 py-1">
                    <button wire:click="$dispatch('openAssignModal', { projectId: {{ $project->id }} })" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Assigner des testeurs
                    </button>
                    <button wire:click="$dispatch('openUatModal', { projectId: {{ $project->id }} })" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        Créer un espace
                    </button>
                    <button wire:click="sendToDeveloper" wire:confirm="Êtes-vous sûr de vouloir envoyer ce projet au développeur ?" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        Envoyer au dev
                    </button>
                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                    @if($project->type === 'iat')
                    <button wire:click="promoteToUat" wire:confirm="Êtes-vous sûr de vouloir valider ce projet et le passer en phase UAT (Recette Client) ?" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Passer en UAT
                    </button>
                    @else
                    <button wire:click="revertToIat" wire:confirm="Êtes-vous sûr de vouloir relancer le cycle IAT ? Cela réinitialisera les statuts de validation client." class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Relancer cycle IAT
                    </button>
                    @if($project->status !== 'completed')
                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                    <button wire:click="closeProject" wire:confirm="Êtes-vous sûr de vouloir clôturer ce projet de manière définitive ?" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Clôturer le projet
                    </button>
                    @endif
                    @endif
                </div>
            </div>

            <!-- action primaire : seule à garder une couleur pleine -->
            <button wire:click="openNewModal" class="px-4 py-2 text-white rounded-xl font-medium text-sm flex items-center gap-2 shadow-sm transition hover:brightness-110 hover:-translate-y-0.5" style="background:#8b0000;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau Cas
            </button>
        </div>
        @endif
    </div>

    @if(auth()->check() && auth()->user()->hasRole('chef_project'))
    {{-- Panel Développeurs Assignés --}}
    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Développeurs assignés au projet
            </h3>
            <button wire:click="openDevModal" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition hover:-translate-y-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Gérer les développeurs
            </button>
        </div>
        @php $devs = $project->developers; @endphp
        @if($devs->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($devs as $dev)
                    <span class="inline-flex items-center gap-1.5 pl-3 pr-1.5 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $dev->name }}
                        <button wire:click="removeDeveloper({{ $dev->id }})" class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition" title="Retirer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Aucun développeur assigné. Cliquez sur "Gérer les développeurs" pour en ajouter.</p>
        @endif
    </div>

    {{-- Modal Gestion Développeurs --}}
    @if($showDevModal)
    <div class="fixed inset-0 z-[70] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/75" wire:click="$set('showDevModal', false)"></div>
            <div class="relative z-10 bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                <div class="px-6 pt-5 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gérer les développeurs</h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        @forelse($this->developersList as $dev)
                            <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                                <input type="checkbox" wire:model="selectedDevIds" value="{{ $dev->id }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-700 dark:text-blue-300 font-semibold text-sm">
                                        {{ strtoupper(substr($dev->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $dev->name }}</span>
                                </div>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 italic">Aucun développeur disponible.</p>
                        @endforelse
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button wire:click="$set('showDevModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">Annuler</button>
                    <button wire:click="saveDevelopers" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif


    {{-- =====================================================================
         Section Retours Client UAT (chef projet uniquement, projet en UAT)
    ====================================================================== --}}
    @if(auth()->check() && auth()->user()->hasRole('chef_project') && $project->type === 'uat')
    @php
        $allCases      = \App\Models\TestCase::where('project_id', $project->id)->get();
        $totalCases    = $allCases->count();
        $validatedCases= $allCases->where('client_status', 'validated')->count();
        $rejectedCases = $allCases->where('client_status', 'rejected')->count();
        $pendingCases  = $allCases->where('client_status', 'pending')->count();
        $progressPct   = $totalCases > 0 ? round(($validatedCases / $totalCases) * 100) : 0;
        $rejectedList  = $allCases->where('client_status', 'rejected')->filter(fn($c) => $c->client_comment);
    @endphp

    <div class="mb-6 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-xl p-5 shadow-sm">
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-purple-800 dark:text-purple-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Retours Client UAT
            </h3>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                {{ $progressPct }}% validés
            </span>
        </div>

        {{-- Compteurs --}}
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="flex flex-col items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $validatedCases }}</span>
                <span class="text-xs text-green-600 dark:text-green-500 mt-0.5">✅ Validés</span>
            </div>
            <div class="flex flex-col items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <span class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $rejectedCases }}</span>
                <span class="text-xs text-red-600 dark:text-red-500 mt-0.5">❌ Rejetés</span>
            </div>
            <div class="flex flex-col items-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $pendingCases }}</span>
                <span class="text-xs text-gray-500 mt-0.5">⏳ En attente</span>
            </div>
        </div>

        {{-- Barre de progression --}}
        @if($totalCases > 0)
        <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progression de validation</span>
                <span>{{ $validatedCases }}/{{ $totalCases }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-green-500 to-green-400 rounded-full transition-all duration-500"
                    style="width: {{ $progressPct }}%">
                </div>
            </div>
        </div>
        @endif

        {{-- Liste des rejets avec commentaires --}}
        @if($rejectedList->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Problèmes signalés par le client
            </h4>
            <div class="space-y-3">
                @foreach($rejectedList as $rCase)
                @php
                    $rData     = $rCase->data ?? [];
                    $rLabel    = $rData['cas_test'] ?? $rData['fonctionnalites'] ?? ('Cas #' . $rCase->id);
                    $rTemplate = $rCase->template;
                    preg_match_all('/!\[capture\]\(([^)]+)\)/', $rCase->client_comment, $rImgs);
                    $rText = preg_replace('/\n?!\[capture\]\([^)]+\)/', '', $rCase->client_comment);
                @endphp
                <div class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">{{ $rLabel }}</p>
                            @if($rTemplate)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $rTemplate->name }}</p>
                            @endif
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">❌ REJETÉ</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $rText }}</p>
                    @foreach($rImgs[1] as $rImg)
                    <a href="{{ $rImg }}" target="_blank" class="mt-2 block">
                        <img src="{{ $rImg }}" class="max-h-24 rounded-lg border border-red-200 hover:opacity-80 transition shadow-sm" alt="capture client">
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @elseif($rejectedCases === 0 && $totalCases > 0)
        <p class="text-xs text-green-600 dark:text-green-400 italic">Aucun problème signalé par le client pour le moment.</p>
        @endif
    </div>
    @endif

    {{-- Liste des Cas de Tests --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">
        @forelse($this->templates as $template)
            @php
                $isTesteur = auth()->check() && auth()->user()->hasRole('tester');
                $link = $isTesteur
                    ? route('testeur.executer', [$project, $template])
                    : route('test-cases.show', [$project, $template]);
                $hasTests = $template->test_cases_count > 0;
            @endphp
            <div class="group h-full">
                <a href="{{ $link }}" class="card-inner block h-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 p-6 transition cursor-pointer hover:-translate-y-1">
                    <div class="flex items-start gap-4 mb-3">
                        <div class="ring-wrap group-hover:scale-110 transition-transform">
                            <svg viewBox="0 0 52 52">
                                <circle class="ring-track" cx="26" cy="26" r="22" fill="none" stroke-width="4"/>
                                @if($hasTests)
                                <circle class="ring-progress" cx="26" cy="26" r="22" fill="none" stroke="#8b0000" stroke-width="4"
                                    stroke-dasharray="138.2" stroke-dashoffset="55.3"/>
                                @endif
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $hasTests ? 'text-[#8b0000]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-[#8b0000] transition-colors line-clamp-2">{{ $template->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Créé le {{ $template->created_at->format('d/m/Y') }}</p>
                        </div>
                        @if(auth()->check() && auth()->user()->hasRole('chef_project'))
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" onclick="event.preventDefault()">
                            <button wire:click.prevent="$dispatch('openAssignModal', { templateId: {{ $template->id }} })" class="p-1.5 text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-md transition" title="Assigner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            </button>
                            <button wire:click.prevent="editTemplate({{ $template->id }})" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click.prevent="deleteTemplate({{ $template->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer le cas '{{ addslashes($template->name) }}' ?" class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        @endif
                    </div>

                    @if($hasTests)
                    <div class="mb-3">
                        <span class="live-badge"><span class="live-dot"></span> En cours</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hasTests ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $hasTests ? 'bg-red-500' : 'bg-gray-400' }}"></span> {{ $template->test_cases_count }} tests
                        </span>
                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:underline">Ouvrir &rarr;</span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun cas de test</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau dossier pour y ajouter vos tests.</p>
            </div>
        @endforelse
    </div>

    <!-- Rapports d'exécution générés -->
    @if(auth()->check() && auth()->user()->hasRole('chef_project'))
    <div class="mt-12 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-[#8b0000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Rapports d'exécution (Executive Reports)
            </h2>
        </div>

        @php $reports = $project->reports->where('status', '!=', 'closed'); @endphp
        
        @if($reports->count() > 0)
            <div class="space-y-4">
                @foreach($reports as $report)
                    @php $stats = $report->stats; @endphp
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $report->title }}</h3>
                                    @if($report->status === 'sent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Transféré au dev</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">À vérifier</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 block mb-1">Périmètre</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $report->perimeter }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block mb-1">Testeur</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $report->responsible }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block mb-1">Version</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $report->tested_version ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block mb-1">Date</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $report->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg flex gap-6 overflow-x-auto">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <div>
                                            <span class="block text-xs text-gray-500">Succès</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $stats['valide'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                        <div>
                                            <span class="block text-xs text-gray-500">Échec</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $stats['non_valide'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                        <div>
                                            <span class="block text-xs text-gray-500">Sous réserve</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $stats['sous_reserve'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                        <div>
                                            <span class="block text-xs text-gray-500">Optimisation</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $stats['optimisation'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">NB / Conclusion :</span>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-md border border-gray-100 dark:border-gray-700">{{ $report->notes }}</p>
                                </div>

                                @if($report->responses && $report->responses->count() > 0)
                                <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Réponses des développeurs</h4>
                                    <div class="space-y-3">
                                        @foreach($report->responses as $response)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-100 dark:border-blue-800">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-blue-800 dark:text-blue-300 text-sm">{{ $response->user->name ?? 'Développeur' }}</span>
                                                <span class="text-xs text-blue-600/70 dark:text-blue-400/70">{{ $response->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @php
                                                preg_match_all('/!\[capture\]\(([^)]+)\)/', $response->content, $rImgs);
                                                $rText = preg_replace('/\n?!\[capture\]\([^)]+\)/', '', $response->content);
                                            @endphp
                                            <p class="text-sm text-blue-900 dark:text-blue-200 whitespace-pre-wrap">{{ $rText }}</p>
                                            @foreach($rImgs[1] as $rImg)
                                            <a href="{{ $rImg }}" target="_blank" class="block mt-2">
                                                <img src="{{ $rImg }}" class="max-h-40 rounded-lg border border-gray-200 dark:border-gray-600 hover:opacity-80 transition shadow-sm" alt="capture développeur">
                                            </a>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md font-medium text-sm text-center transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Télécharger
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                        class="absolute right-0 top-full mt-1 w-44 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                                        <a href="{{ route('rapports.export', ['report' => $report->id, 'format' => 'pdf']) }}" target="_blank"
                                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            Exporter en PDF
                                        </a>
                                        <a href="{{ route('rapports.export', ['report' => $report->id, 'format' => 'word']) }}" target="_blank"
                                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 transition border-t border-gray-100 dark:border-gray-600">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Exporter en Word
                                        </a>
                                    </div>
                                </div>
                                @if(!in_array($report->status, ['sent', 'resolved', 'closed', 'retest']))
                                <button wire:click="sendReportToDev({{ $report->id }})" wire:confirm="Transférer ce rapport détaillé aux développeurs assignés ?" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm text-center transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Transférer au dev
                                </button>
                                <button wire:click="validateCorrection({{ $report->id }})" wire:confirm="Tout est correct, clôturer directement ce rapport ?" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm text-center transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Valider directement
                                </button>
                                @elseif($report->status === 'sent')
                                <button disabled class="px-4 py-2 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-md font-medium text-sm text-center cursor-not-allowed flex items-center justify-center gap-2 border border-yellow-200 dark:border-yellow-800">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    En attente (Dev)
                                </button>
                                @elseif($report->status === 'resolved')
                                <button wire:click="validateCorrection({{ $report->id }})" wire:confirm="Valider la correction du développeur et clôturer ce rapport ?" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm text-center transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Valider la correction
                                </button>
                                <button wire:click="rejectToTester({{ $report->id }})" wire:confirm="Renvoyer ce rapport au testeur pour re-vérification ?" class="px-4 py-2 bg-orange-700 hover:bg-orange-800 text-white rounded-md font-medium text-sm text-center transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Renvoyer au testeur
                                </button>
                                <button wire:click="relaunchDev({{ $report->id }})" wire:confirm="Relancer le développeur ? La correction actuelle n'est pas satisfaisante." class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium text-sm text-center transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    Relancer le dev
                                </button>
                                @elseif($report->status === 'retest')
                                <button disabled class="px-4 py-2 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-md font-medium text-sm text-center cursor-not-allowed flex items-center justify-center gap-2 border border-amber-200 dark:border-amber-800">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    En re-test (Testeur)
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun rapport</h3>
                <p class="mt-1 text-sm text-gray-500">Les testeurs n'ont pas encore généré de rapport pour ce projet.</p>
            </div>
        @endif
    </div>
    @endif

    <!-- Historique du Projet -->
    <div class="mt-8 mb-8">
        <livewire:project-activity-log :projectId="$project->id" />
    </div>

    <!-- Modal Création de Cas de Test -->
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                <form wire:submit="save">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">{{ $editMode ? 'Modifier le fichier ' : 'Créer un nouveau fichier ' }}</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du fichier / Cas de test</label>
                                <input type="text" wire:model="name" placeholder="Ex: Fichier UAT - Portail Services" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Liens d'accès spécifiques au cas de test (URLs utiles)</label>
                                <button type="button" wire:click="addLink" class="text-sm text-[#8b0000] hover:text-red-800 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Ajouter un lien
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($links as $index => $link)
                                <div class="flex gap-2 items-start">
                                    <div class="flex-1 space-y-2">
                                        <input type="text" wire:model="links.{{ $index }}.title" placeholder="Titre (ex: Page de paiement)" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                        @error('links.'.$index.'.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        
                                        <input type="url" wire:model="links.{{ $index }}.url" placeholder="URL (ex: https://...)" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                        @error('links.'.$index.'.url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="button" wire:click="removeLink({{ $index }})" class="mt-1 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#8b0000] text-base font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:ml-3 sm:w-auto sm:text-sm">
                            Enregistrer
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    <livewire:assign-testers />
    <livewire:create-uat-space />

    {{-- Drawer : Rapports Archivés (clôturés) --}}
    @php $closedReports = $project->reports->where('status', 'closed'); @endphp

    @if($showArchives)
    {{-- Overlay --}}
    <div
        class="fixed inset-0 bg-black/40 z-[70]"
        wire:click="$set('showArchives', false)"
    ></div>

    {{-- Panneau latéral --}}
    <div class="fixed top-0 right-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl z-[80] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.707 11.293A1 1 0 007.697 20h8.606a1 1 0 00.99-.707L19 8M10 12h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 dark:text-white text-sm">Rapports archivés</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $closedReports->count() }} rapport(s) clôturé(s)</p>
                </div>
            </div>
            <button wire:click="$set('showArchives', false)" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Contenu --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @if($closedReports->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.707 11.293A1 1 0 007.697 20h8.606a1 1 0 00.99-.707L19 8"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucun rapport archivé</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Les rapports validés apparaîtront ici</p>
                </div>
            @else
                @foreach($closedReports as $report)
                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl p-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $report->type === 'iat' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ strtoupper($report->type) }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Clôturé
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $report->title }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $report->perimeter }}</p>
                        </div>
                    </div>

                    @if($report->stats)
                    <div class="flex items-center gap-3 text-xs mb-3 flex-wrap font-medium">
                        <span class="text-green-600">Validés: {{ $report->stats['valide'] ?? 0 }}</span>
                        <span class="text-red-600">Échecs: {{ $report->stats['non_valide'] ?? 0 }}</span>
                        <span class="text-amber-600">Sous réserve: {{ $report->stats['sous_reserve'] ?? 0 }}</span>
                        <span class="text-blue-600">Optimisation: {{ $report->stats['optimisation'] ?? 0 }}</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                        <span>Par {{ $report->creator?->name }}</span>
                        <span>{{ $report->updated_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="mt-3" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                           class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Télécharger
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                            class="mt-1 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                            <a href="{{ route('rapports.export', ['report' => $report->id, 'format' => 'pdf']) }}" target="_blank"
                                class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Exporter en PDF
                            </a>
                            <a href="{{ route('rapports.export', ['report' => $report->id, 'format' => 'word']) }}" target="_blank"
                                class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 transition border-t border-gray-100 dark:border-gray-600">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Exporter en Word
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

</div>
