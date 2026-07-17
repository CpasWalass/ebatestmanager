<div>
@if($showModal)
<div class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <:div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
    <div class="flex items-center justify-center min-h-screen px-4 py-10">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full border border-gray-200 dark:border-gray-700
            {{ count($reviewRows) ? 'sm:max-w-4xl' : 'sm:max-w-lg' }}">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                {{-- ===================== ÉTAPE 1 : FORMULAIRE ===================== --}}
                @if(!$generationRequestId && !count($reviewRows))
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-[#8b0000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Générer des cas de test avec l'IA</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">À partir d'un cahier des charges ou d'une description de workflow, l'IA propose des cas de test que vous pourrez relire avant tout ajout.</p>

                    @if($errorMessage)
                        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-md text-sm">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <div class="flex gap-2 mb-4 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg text-sm font-medium">
                        <button type="button" wire:click="setInputMode('file')"
                            class="flex-1 py-1.5 rounded-md transition {{ $inputMode === 'file' ? 'bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500' }}">
                            Cahier des charges (fichier)
                        </button>
                        <button type="button" wire:click="setInputMode('text')"
                            class="flex-1 py-1.5 rounded-md transition {{ $inputMode === 'text' ? 'bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500' }}">
                            Décrire un workflow
                        </button>
                    </div>

                    @if($inputMode === 'file')
                        <label class="block border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-[#8b0000] transition mb-2">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $sourceFile ? $sourceFile->getClientOriginalName() : 'Cliquez pour choisir un fichier PDF ou Word' }}
                            </span>
                            <input type="file" wire:model="sourceFile" class="hidden" accept=".pdf,.doc,.docx,.txt">
                        </label>
                        @error('sourceFile') <p class="text-xs text-red-600 mb-3">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="sourceFile" class="text-xs text-gray-400 mb-3">Chargement du fichier…</div>
                    @else
                        <textarea wire:model="workflowText" rows="6"
                            placeholder="Décrivez le workflow ou les fonctionnalités à tester…"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-[#8b0000]"></textarea>
                        @error('workflowText') <p class="text-xs text-red-600 mb-3">{{ $message }}</p> @enderror
                    @endif

                    <div class="mb-5 mt-3">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1.5">Modèle ciblé</label>
                        <div class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                            {{ $template->name }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Les cas générés respecteront les colonnes de ce modèle.</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Annuler</button>
                        <button type="button" wire:click="startGeneration" wire:loading.attr="disabled" wire:target="startGeneration"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#8b0000] hover:bg-[#6b0000] flex items-center gap-2 disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <span wire:loading.remove wire:target="startGeneration">Générer</span>
                            <span wire:loading wire:target="startGeneration">Analyse en cours…</span>
                        </button>
                    </div>

                {{-- ===================== ÉTAPE 2 : GÉNÉRATION EN COURS ===================== --}}
                @elseif($generationRequestId && !count($reviewRows) && !$errorMessage)
                    <div wire:poll.2s="refreshStatus" class="flex flex-col items-center justify-center py-14">
                        <svg class="animate-spin h-8 w-8 text-[#8b0000] mb-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Génération des cas de test en cours…</p>
                        <p class="text-xs text-gray-400 mt-1">Cela peut prendre jusqu'à une minute selon la taille du document.</p>
                    </div>

                @elseif($errorMessage)
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-lg text-sm mb-4">
                        {{ $errorMessage }}
                    </div>
                    <div class="flex justify-end">
                        <button type="button" wire:click="restartGeneration" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#8b0000] hover:bg-[#6b0000]">Réessayer</button>
                    </div>

                {{-- ===================== ÉTAPE 3 : RELECTURE ===================== --}}
                @else
                    @php $selectedCount = collect($reviewRows)->where('selected', true)->count(); @endphp

                    <div class="flex items-center justify-between bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 mb-4">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            {{ count($reviewRows) }} cas de test proposés — à relire avant ajout
                        </div>
                        <label class="flex items-center gap-2 text-xs text-amber-700 dark:text-amber-300">
                            <input type="checkbox" checked wire:click="toggleSelectAll($event.target.checked)" class="rounded"> Tout sélectionner
                        </label>
                    </div>

                    <div class="overflow-auto rounded-xl border border-gray-200 dark:border-gray-700" style="max-height:360px;">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 w-8"></th>
                                    <th class="px-3 py-2 text-left">Cas de test</th>
                                    <th class="px-3 py-2 text-left">Modules</th>
                                    <th class="px-3 py-2 text-left">Scénario</th>
                                    <th class="px-3 py-2 text-left">Résultat attendu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($reviewRows as $i => $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 {{ !($row['selected'] ?? false) ? 'opacity-50' : '' }}">
                                    <td class="px-3 py-2"><input type="checkbox" wire:model.live="reviewRows.{{ $i }}.selected" class="rounded"></td>
                                    <td class="px-3 py-2"><input wire:model.blur="reviewRows.{{ $i }}.cas_test" class="w-full bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-[#8b0000] rounded px-2 py-1"></td>
                                    <td class="px-3 py-2"><input wire:model.blur="reviewRows.{{ $i }}.modules" class="w-full bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300"></td>
                                    <td class="px-3 py-2"><input wire:model.blur="reviewRows.{{ $i }}.scenarios_test" class="w-full bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300"></td>
                                    <td class="px-3 py-2"><input wire:model.blur="reviewRows.{{ $i }}.resultats_attendus" class="w-full bg-transparent border border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <button type="button" wire:click="restartGeneration" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">← Relancer une génération</button>
                        <div class="flex gap-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Tout rejeter</button>
                            <button type="button" wire:click="confirmSelection" wire:loading.attr="disabled" wire:target="confirmSelection"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-60">
                                Ajouter les {{ $selectedCount }} cas sélectionnés
                            </button>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">Rien n'est enregistré tant que vous n'avez pas cliqué sur « Ajouter ».</p>
                @endif

            </div>
        </div>
    </div>
</:div>
@endif
</div>
