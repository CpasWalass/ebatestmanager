


<div>
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold">Projets</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Gestion des projets de test</p>
        </div>
        
        @if(auth()->check() && auth()->user()->hasRole('chef_project'))
        <button wire:click="openNewModal" class="px-5 py-2.5 bg-[#8b0000] hover:bg-[#a10000] text-white rounded-xl font-semibold text-sm flex items-center space-x-2 transition shadow-sm hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nouveau Projet</span>
        </button>
        @endif
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live="search" type="text" placeholder="Rechercher un projet..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b0000] transition">
            </div>
            <div class="w-full sm:w-48">
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8b0000] transition">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="en_cours">En cours</option>
                    <option value="in_review">En révision</option>
                    <option value="completed">Terminé</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grille des projets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">
        @forelse($this->projects as $project)
            @php
                if (auth()->check() && auth()->user()->hasRole('tester')) {
                    $projLink = route('testeur.projets.show', $project);
                } else {
                    $projLink = route('projets.show', $project);
                }
            @endphp
            <a href="{{ $projLink }}" class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 p-6 transition group cursor-pointer hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-[10px] font-bold rounded-md uppercase tracking-wider mb-3">
                            {{ $project->type ?? 'Web' }}
                        </span>
                        @php
                            $rejectedUatCount = \App\Models\TestCase::where('project_id', $project->id)
                                ->where('client_status', 'rejected')
                                ->count();
                        @endphp
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#8b0000] transition-colors line-clamp-1">{{ $project->name }}</h3>
                            @if($rejectedUatCount > 0)
                            <span class="relative flex h-3 w-3" title="{{ $rejectedUatCount }} cas rejetés en UAT">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        @php
                            $statusColors = [
                                'en_attente' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                'en_cours' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                'in_review' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                'completed' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800',
                            ];
                            $statusLabels = [
                                'en_attente' => 'En attente',
                                'en_cours' => 'En cours',
                                'in_review' => 'En révision',
                                'completed' => 'Terminé',
                            ];
                            $statusClass = $statusColors[$project->status] ?? $statusColors['en_attente'];
                            $statusLabel = $statusLabels[$project->status] ?? 'En attente';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        @if(auth()->check() && auth()->user()->hasRole('chef_project'))
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click.prevent="editProject({{ $project->id }})" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click.prevent="deleteProject({{ $project->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer le projet '{{ addslashes($project->name) }}' ?" class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 line-clamp-2 min-h-[40px]">
                    {{ $project->description ?: 'Aucune description pour ce projet.' }}
                </p>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs font-medium text-gray-500">{{ $project->test_cases_count }} tests</span>
                    </div>
                    <span class="text-xs font-medium text-gray-400">{{ $project->created_at->format('d/m/Y') }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400">
                Aucun projet trouvé.
            </div>
        @endforelse
    </div>

    <!-- Modal Création de projet -->
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                <form wire:submit.prevent="save">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">{{ $editMode ? 'Modifier le projet' : 'Créer un nouveau projet' }}</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du projet</label>
                                    <input type="text" wire:model="name" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client</label>
                                    <select wire:model="client_id" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                        <option value="">Sélectionner un client...</option>
                                        @foreach($this->clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Version</label>
                                    <input type="text" wire:model="version" placeholder="ex: 1.0.0" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                    @error('version') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de test</label>
                                    <select wire:model="type" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                        <option value="IAT">IAT</option>
                                        <option value="UAT">UAT</option>
                                    </select>
                                    @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Périmètre</label>
                                <textarea wire:model="perimeter" rows="2" placeholder="Périmètre des tests..." class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]"></textarea>
                                @error('perimeter') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Liens d'accès (URLs utiles)</label>
                                <button type="button" wire:click="addLink" class="text-sm text-[#8b0000] hover:text-red-800 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Ajouter un lien
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($links as $index => $link)
                                <div class="flex gap-2 items-start">
                                    <div class="flex-1 space-y-2">
                                        <input type="text" wire:model="links.{{ $index }}.title" placeholder="Titre (ex: Portail UAT)" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
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
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-[#8b0000] text-base font-semibold text-white hover:bg-[#a10000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:ml-3 sm:w-auto sm:text-sm transition hover:-translate-y-0.5">
                            Enregistrer
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-5 py-2.5 bg-white dark:bg-gray-700 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>