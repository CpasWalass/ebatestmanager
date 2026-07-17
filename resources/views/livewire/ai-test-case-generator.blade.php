<div>
    @if($showModal)
    {{--
        Le modal est rendu dans un portail Livewire standard.
        IMPORTANT : pas de backdrop intercептant les clics.
    --}}
    <div
        style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;"
        aria-modal="true"
        role="dialog"
    >
        {{-- Overlay sombre (pointer-events:none = ne bloque pas les clics) --}}
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.6);pointer-events:none;"></div>

        {{-- Boîte du modal --}}
        <div style="position:relative;z-index:10;width:100%;max-width:{{ count($reviewRows) ? '64rem' : '32rem' }};background:white;border-radius:1rem;box-shadow:0 25px 50px rgba(0,0,0,0.3);overflow:hidden;"
             class="dark:bg-gray-800">

            <div class="px-6 pt-6 pb-6">

                {{-- ===== ÉTAPE 1 : SAISIE ===== --}}
                @if(! $generationRequestId && ! count($reviewRows))
                <div wire:key="step-1">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-[#8b0000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Générer des cas de test avec l'IA</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        L'IA analyse votre document et propose jusqu'à 15 cas de test adaptés au modèle sélectionné.
                    </p>

                    @if($errorMessage)
                        <div class="mb-4 p-3 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    {{-- Onglets Fichier / Texte --}}
                    <div class="flex gap-1 mb-4 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg text-sm">
                        <button type="button" wire:click="setInputMode('file')"
                                class="flex-1 py-2 rounded-md transition font-medium
                                       {{ $inputMode === 'file' ? 'bg-white shadow text-gray-900 dark:bg-gray-800 dark:text-white' : 'text-gray-500' }}">
                            📄 Fichier (PDF/Word)
                        </button>
                        <button type="button" wire:click="setInputMode('text')"
                                class="flex-1 py-2 rounded-md transition font-medium
                                       {{ $inputMode === 'text' ? 'bg-white shadow text-gray-900 dark:bg-gray-800 dark:text-white' : 'text-gray-500' }}">
                            ✏️ Décrire le workflow
                        </button>
                    </div>

                    @if($inputMode === 'file')
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600
                                      rounded-xl p-8 cursor-pointer hover:border-[#8b0000] transition mb-3">
                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $sourceFile ? $sourceFile->getClientOriginalName() : 'Cliquez pour choisir un fichier PDF, Word ou TXT' }}
                            </span>
                            <input type="file" wire:model="sourceFile" class="hidden" accept=".pdf,.doc,.docx,.txt">
                        </label>
                        @error('sourceFile')
                            <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="sourceFile" class="text-xs text-gray-400 mb-2">Chargement…</div>
                    @else
                        <textarea
                            wire:model="workflowText"
                            rows="7"
                            placeholder="Décrivez les fonctionnalités à tester, les flux utilisateur, les règles métier…"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white
                                   rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-[#8b0000] resize-none"
                        ></textarea>
                        @error('workflowText')
                            <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
                        @enderror
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300 mb-5 border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-semibold uppercase text-gray-400">Modèle ciblé :</span>
                        {{ $template->name }}
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Annuler
                        </button>
                        <button type="button" wire:click="startGeneration"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#8b0000] hover:bg-[#6b0000] flex items-center gap-2 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            <span wire:loading.remove wire:target="startGeneration">Générer</span>
                            <span wire:loading wire:target="startGeneration">Analyse en cours…</span>
                        </button>
                    </div>
                </div>
                {{-- ===== ERREUR ===== --}}
                @elseif($errorMessage)
                <div wire:key="step-error">
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-lg text-sm mb-4">
                        ⚠️ {{ $errorMessage }}
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                            Fermer
                        </button>
                        <button type="button" wire:click="restartGeneration"
                                class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#8b0000] hover:bg-[#6b0000]">
                            Réessayer
                        </button>
                    </div>
                </div>
                {{-- ===== ÉTAPE 3 : RELECTURE ===== --}}
                @else
                <div wire:key="step-3">
                    @php $selectedCount = collect($reviewRows)->where('selected', true)->count(); @endphp

                    <div class="flex items-center justify-between mb-4 bg-amber-50 dark:bg-amber-900/20
                                border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3">
                        <span class="text-sm font-medium text-amber-800 dark:text-amber-300">
                            {{ count($reviewRows) }} cas proposés — sélectionnez ceux à importer
                        </span>
                        <div class="flex gap-3">
                            <button type="button" wire:click="toggleSelectAll(1)"
                                    class="text-xs text-amber-700 dark:text-amber-400 underline hover:no-underline">
                                Tout cocher
                            </button>
                            <button type="button" wire:click="toggleSelectAll(0)"
                                    class="text-xs text-amber-700 dark:text-amber-400 underline hover:no-underline">
                                Tout décocher
                            </button>
                        </div>
                    </div>

                    <div class="overflow-auto rounded-xl border border-gray-200 dark:border-gray-700 mb-4" style="max-height:340px;">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2 w-8 text-center">✓</th>
                                    <th class="px-3 py-2 text-left">Cas de test</th>
                                    <th class="px-3 py-2 text-left">Module</th>
                                    <th class="px-3 py-2 text-left">Scénario</th>
                                    <th class="px-3 py-2 text-left">Résultat attendu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($reviewRows as $i => $row)
                                <tr wire:key="row-{{ $i }}" class="{{ ! ($row['selected'] ?? false) ? 'opacity-40' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                               wire:model.live="reviewRows.{{ $i }}.selected"
                                               class="rounded border-gray-300">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input wire:model.blur="reviewRows.{{ $i }}.cas_test"
                                               class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-[#8b0000] rounded px-2 py-1 text-gray-800 dark:text-gray-200">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input wire:model.blur="reviewRows.{{ $i }}.modules"
                                               class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input wire:model.blur="reviewRows.{{ $i }}.scenarios_test"
                                               class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input wire:model.blur="reviewRows.{{ $i }}.resultats_attendus"
                                               class="w-full bg-transparent border border-transparent hover:border-gray-300 focus:border-[#8b0000] rounded px-2 py-1 text-gray-600 dark:text-gray-300">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="button" wire:click="restartGeneration"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            ← Nouvelle génération
                        </button>
                        <div class="flex gap-3">
                            <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                                Annuler
                            </button>
                            <button type="button" @click="$wire.saveAiCases()"
                                    class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition">
                                ✓ Ajouter {{ $selectedCount }} cas
                            </button>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-gray-400">Rien n'est enregistré avant de cliquer sur « Ajouter ».</p>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif
</div>
