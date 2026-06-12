


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
        <div class="flex items-center gap-2">
            <button wire:click="$dispatch('openAssignModal')" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md font-medium text-sm transition shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Assigner testeurs
            </button>
            <button wire:click="sendToDeveloper" wire:confirm="Êtes-vous sûr de vouloir envoyer ce projet au développeur ?" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Envoyer au dev
            </button>
            <button wire:click="$dispatch('openUatModal', { projectId: {{ $project->id }} })" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md font-medium text-sm transition shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                Créer espace UAT
            </button>
            <button wire:click="$set('showModal', true)" class="px-4 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Nouveau Cas</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Liste des Cas de Tests -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">
        @forelse($this->templates as $template)
            @php
                $link = (auth()->check() && auth()->user()->hasRole('tester'))
                    ? route('testeur.executer', [$project, $template])
                    : route('test-cases.show', [$project, $template]);
            @endphp
            <a href="{{ $link }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 p-6 transition group cursor-pointer">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-lg bg-red-50 dark:bg-red-900/20 text-[#8b0000] dark:text-red-400 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-[#8b0000] transition-colors line-clamp-2">{{ $template->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Créé le {{ $template->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $template->test_cases_count }} tests
                        </span>
                    </div>
                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:underline">
                        Ouvrir le tableur &rarr;
                    </span>
                </div>
            </a>
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

    <!-- Modal Création de Cas de Test -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700">
                <form wire:submit="save">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Créer un nouveau fichier UAT</h3>
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
</div>
