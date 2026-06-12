


<div class="h-[calc(100vh-180px)] flex flex-col">
    <!-- Fil d'Ariane & En-tête -->
    <div class="mb-4">
        <nav class="flex mb-2 text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    @php
                        $isTester = auth()->check() && auth()->user()->hasRole('tester');
                        $backProjets = $isTester ? route('testeur.projets.index') : route('projets.index');
                        $backProject = $isTester ? route('testeur.projet.show', $project) : route('projets.show', $project);
                    @endphp
                    <a href="{{ $backProjets }}" class="hover:text-gray-900 dark:hover:text-white transition">Projets</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ $backProject }}" class="hover:text-gray-900 dark:hover:text-white transition">{{ $project->name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-900 dark:text-white">{{ $template->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        </nav>

        @if(count($this->allLinks) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($this->allLinks as $link)
            <a href="{{ $link['url'] }}" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md text-sm font-medium flex items-center gap-1.5 transition border border-blue-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                {{ $link['title'] }}
            </a>
            @endforeach
        </div>
        @endif

        <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h1 class="text-xl font-bold uppercase tracking-wide text-gray-900 dark:text-white">
                <span class="text-[#8b0000]">FICHIER UAT</span> - {{ $template->name }}
            </h1>
            @if(!auth()->check() || (!auth()->user()->hasRole('tester') && !auth()->user()->hasRole('developer')))
            <div class="flex items-center space-x-2">
                <button wire:click="$set('showImportModal', true)" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Importer Excel</span>
                </button>
                <button wire:click="$set('showColumnModal', true)" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Gérer les colonnes</span>
                </button>
                <button wire:click="addRow" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Ajouter une ligne</span>
                </button>
            </div>
            @elseif(auth()->check() && auth()->user()->hasRole('tester'))
            <button wire:click="$dispatch('openReportModal', { projectId: {{ $project->id }}, templateId: {{ $template->id }} })" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Générer le rapport</span>
            </button>
            @endif
        </div>
        <livewire:report-generator />
    </div>

    <!-- Tableur Type Excel -->
    <div class="flex-1 overflow-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 pb-20">
        <table class="w-full text-sm text-left border-collapse min-w-max">
            <thead class="text-xs text-white uppercase" style="background-color: #1a4f3e; /* Couleur verte style Excel */">
                <tr>
                    <th scope="col" class="px-2 py-3 border-r border-[#133c2e] text-center w-10">#</th>
                    @foreach($template->fields as $field)
                        <th scope="col" class="px-4 py-3 border-r border-[#133c2e] whitespace-nowrap">
                            {{ $field['label'] }}
                        </th>
                    @endforeach
                    @if(!auth()->check() || (!auth()->user()->hasRole('tester') && !auth()->user()->hasRole('developer')))
                    <th scope="col" class="px-2 py-3 text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($this->rows as $index => $row)
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 group transition-colors">
                        <td class="px-2 py-2 border-r border-gray-200 dark:border-gray-700 text-center font-medium text-gray-500 bg-gray-50 dark:bg-gray-800/50">
                            {{ $index + 1 }}
                        </td>
                        
                        @foreach($template->fields as $field)
                            @php
                                $val = $row->data[$field['name']] ?? '';
                                // Code couleur pour STATUS
                                $bgClass = '';
                                if ($field['name'] === 'status' || $field['name'] === 'etat_test') {
                                    if (in_array(strtolower($val), ['validé', 'terminé'])) $bgClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
                                    elseif (in_array(strtolower($val), ['optimisation', 'en cours'])) $bgClass = 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300';
                                    elseif (in_array(strtolower($val), ['sous réserve', 'non validé', 'échec'])) $bgClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                                }
                            @endphp

                                @php
                                    $isTester = auth()->check() && auth()->user()->hasRole('tester');
                                    $isDev = auth()->check() && auth()->user()->hasRole('developer');
                                    $testerEditableFields = ['resultats_obtenus', 'nature', 'status', 'commentaires'];
                                    
                                    $isReadOnly = false;
                                    if ($isTester) {
                                        $isReadOnly = !in_array($field['name'], $testerEditableFields);
                                    } elseif ($isDev) {
                                        $isReadOnly = $field['name'] !== 'commentaires';
                                    }
                                @endphp
                                
                                <td class="p-0 border-r border-gray-200 dark:border-gray-700 {{ $bgClass }} relative">
                                    @if($isReadOnly)
                                        <div class="w-full h-full min-h-[40px] px-3 py-2 text-gray-700 dark:text-gray-300 {{ $field['type'] === 'textarea' ? 'whitespace-pre-wrap' : '' }}">
                                            @if($field['type'] === 'url' && $val)
                                                <a href="{{ $val }}" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1">
                                                    {{ $val }}
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                {{ $val }}
                                            @endif
                                        </div>
                                    @elseif($field['type'] === 'url')
                                        <div class="flex items-center w-full h-full min-h-[40px] bg-transparent focus-within:ring-2 focus-within:ring-[#8b0000] focus-within:bg-white dark:focus-within:bg-gray-700">
                                            <input 
                                                type="url" 
                                                value="{{ $val }}"
                                                wire:blur="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                                class="flex-1 px-3 py-2 bg-transparent border-none outline-none w-full"
                                                placeholder="https://..."
                                            >
                                            @if($val)
                                            <a href="{{ $val }}" target="_blank" class="px-2 text-blue-500 hover:text-blue-700 flex-shrink-0" title="Ouvrir le lien">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                            @endif
                                        </div>
                                @elseif($field['type'] === 'select' && isset($field['options']))
                                    <select 
                                        wire:change="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                        class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none appearance-none {{ $bgClass ? 'font-semibold' : '' }}"
                                    >
                                        <option value=""></option>
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option }}" @selected($val === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field['type'] === 'textarea')
                                    <textarea 
                                        wire:blur="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                        class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none resize-none overflow-hidden"
                                        rows="1"
                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                    >{{ $val }}</textarea>
                                @else
                                    <input 
                                        type="text" 
                                        value="{{ $val }}"
                                        wire:blur="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                        class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none"
                                    >
                                @endif
                            </td>
                        @endforeach
                        
                        @if(!auth()->check() || (!auth()->user()->hasRole('tester') && !auth()->user()->hasRole('developer')))
                        <td class="px-2 py-2 text-center">
                            <button wire:click="deleteRow({{ $row->id }})" wire:confirm="Supprimer cette ligne ?" class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            Aucune ligne de test. Cliquez sur "Ajouter une ligne" pour commencer.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        /* Ajustements style Excel */
        table td input, table td textarea, table td select {
            border-radius: 0;
            width: 100%;
        }
        table td {
            vertical-align: top;
        }
        table td textarea {
            white-space: pre-wrap;
            min-width: 150px;
        }
        table th {
            letter-spacing: 0.05em;
        }
    </style>

    <!-- Modal Gestion des Colonnes -->
    @if($showColumnModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showColumnModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-gray-700">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Gérer les colonnes</h3>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Colonnes existantes</h4>
                        <ul class="space-y-2 max-h-60 overflow-y-auto pr-2">
                            @foreach($template->fields as $field)
                                <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $field['label'] }}</span>
                                        <span class="text-xs text-gray-500">Type: {{ $field['type'] }}</span>
                                    </div>
                                    <button wire:click="removeColumn('{{ $field['name'] }}')" wire:confirm="Supprimer la colonne '{{ $field['label'] }}' ? Attention, cela n'efface pas les données existantes, mais elles ne seront plus affichées." class="text-red-500 hover:text-red-700 p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Ajouter une colonne</h4>
                        <div class="flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Nom de la colonne</label>
                                <input type="text" wire:model="newColumnName" placeholder="Ex: Priorité, Lien Jira..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                @error('newColumnName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-1/3">
                                <label class="block text-xs text-gray-500 mb-1">Type de champ</label>
                                <select wire:model="newColumnType" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                    <option value="text">Texte court</option>
                                    <option value="textarea">Texte long</option>
                                    <option value="select">Menu déroulant (Liste)</option>
                                    <option value="url">Lien URL cliquable</option>
                                </select>
                                @error('newColumnType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button wire:click="addColumn" class="px-4 py-2 bg-[#8b0000] hover:bg-red-800 text-white rounded-md text-sm font-medium transition shadow-sm h-10 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 flex justify-end border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showColumnModal', false)" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Import Excel -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showImportModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">Importer un fichier Excel</h3>
                    <p class="text-sm text-gray-500 mb-4">L'importateur va chercher automatiquement la ligne d'en-tête (où se trouvent vos noms de colonnes). Assurez-vous que les colonnes de votre fichier correspondent aux colonnes de cette grille.</p>
                    
                    @if($importResult)
                        <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-200 rounded-md text-sm">
                            {{ $importResult }}
                        </div>
                    @endif

                    @if($importError)
                        <div class="mb-4 p-3 bg-red-50 text-red-700 border border-red-200 rounded-md text-sm">
                            {{ $importError }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sélectionner un fichier (.xlsx)</label>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#8b0000] file:text-white hover:file:bg-red-800 transition">
                        @error('excelFile') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        
                        <div wire:loading wire:target="excelFile" class="text-sm text-blue-500 mt-2">Chargement du fichier en cours...</div>
                        <div wire:loading wire:target="importExcel" class="text-sm text-blue-500 mt-2">Importation en cours, veuillez patienter...</div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showImportModal', false)" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:text-sm">
                        Fermer
                    </button>
                    <button type="button" wire:click="importExcel" wire:loading.attr="disabled" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#8b0000] text-base font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:text-sm disabled:opacity-50">
                        Lancer l'importation
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>