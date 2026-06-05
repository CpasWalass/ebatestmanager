


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

        <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h1 class="text-xl font-bold uppercase tracking-wide text-gray-900 dark:text-white">
                <span class="text-[#8b0000]">FICHIER UAT</span> - {{ $template->name }}
            </h1>
            @if(!auth()->check() || (!auth()->user()->hasRole('tester') && !auth()->user()->hasRole('developer')))
            <button wire:click="addRow" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Ajouter une ligne</span>
            </button>
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
                                
                                @if($isReadOnly)
                                    <div class="w-full h-full min-h-[40px] px-3 py-2 text-gray-700 dark:text-gray-300 {{ $field['type'] === 'textarea' ? 'whitespace-pre-wrap' : '' }}">
                                        {{ $val }}
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
</div>