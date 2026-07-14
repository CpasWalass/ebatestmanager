


<div class="h-[calc(100vh-180px)] flex flex-col">
    <!-- Fil d'Ariane & En-tête -->
    <div class="mb-4">
        <nav class="flex mb-2 text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    @php
                        $isTester = auth()->check() && auth()->user()->hasRole('tester');
                        $isClient = auth()->check() && auth()->user()->hasRole('client');
                        $backProjets = $isTester ? route('testeur.projets.index') : route('projets.index');
                        $backProject = $isTester
                            ? route('testeur.projets.show', $project)
                            : route('projets.show', $project);
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
            @if(auth()->check() && auth()->user()->hasRole('chef_project'))
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
            @elseif(auth()->check() && auth()->user()->hasRole('client'))
            {{-- Le client voit juste un indicateur de son rôle — ses saisies se sauvegardent automatiquement --}}
            <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span class="text-xs font-medium text-purple-700 dark:text-purple-300">Vue Client UAT — Vos retours se sauvegardent automatiquement</span>
            </div>
            @else
            <div class="flex items-center space-x-2">
                <button wire:click="openCommitModal" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm shadow-green-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Soumettre ma session</span>
                </button>
            </div>
            @if(auth()->check() && auth()->user()->hasRole('tester'))
            <button wire:click="$dispatch('openReportModal', { projectId: {{ $project->id }}, templateId: {{ $template->id }} })" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm flex items-center space-x-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Générer le rapport</span>
            </button>
            @endif
            @endif
        </div>
        <livewire:report-generator />
    </div>

    <!-- Tableur Type Excel -->
    <div class="flex-1 overflow-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 pb-20">
        <table class="w-full text-sm text-left border-collapse min-w-max" style="table-layout: fixed;">
            <thead class="text-xs text-white uppercase" style="background-color: #1a4f3e; /* Couleur verte style Excel */">
                <tr>
                    <th scope="col" class="px-2 py-3 border-r border-[#133c2e] text-center w-10">#</th>
                    @foreach($template->fields as $field)
                        <th scope="col" 
                            class="px-4 py-3 border-r border-[#133c2e] whitespace-nowrap relative select-none"
                            x-data="{
                                width: {{ $field['type'] === 'textarea' ? 250 : 150 }},
                                startX: 0,
                                startWidth: 0,
                                startResize(e) {
                                    this.startX = e.pageX;
                                    this.startWidth = this.$el.offsetWidth;
                                    const onMouseMove = (e) => {
                                        this.width = Math.max(60, this.startWidth + (e.pageX - this.startX));
                                    };
                                    const onMouseUp = () => {
                                        document.removeEventListener('mousemove', onMouseMove);
                                        document.removeEventListener('mouseup', onMouseUp);
                                        document.body.style.cursor = 'default';
                                    };
                                    document.body.style.cursor = 'col-resize';
                                    document.addEventListener('mousemove', onMouseMove);
                                    document.addEventListener('mouseup', onMouseUp);
                                }
                            }"
                            :style="`width: ${width}px; min-width: ${width}px; max-width: ${width}px`"
                        >
                            <div class="overflow-hidden text-ellipsis">{{ $field['label'] }}</div>
                            <div class="absolute right-0 top-0 bottom-0 z-20"
                                 style="width: 10px; cursor: col-resize; transform: translateX(5px); background: transparent;"
                                 onmouseover="this.style.background='rgba(74, 222, 128, 0.5)'"
                                 onmouseout="this.style.background='transparent'"
                                 @mousedown.prevent="startResize"></div>
                        </th>
                    @endforeach
                    @if(auth()->check() && auth()->user()->hasRole('chef_project'))
                    <th scope="col" class="px-2 py-3 text-center" style="width: 100px;">Actions</th>
                    @endif
                    {{-- Colonne AVIS CLIENT : visible client et chef --}}
                    @if(auth()->check() && (auth()->user()->hasRole('client') || auth()->user()->hasRole('chef_project')))
                    <th scope="col" 
                        class="px-3 py-3 text-center border-r border-[#133c2e] whitespace-nowrap relative select-none"
                        x-data="{
                            width: 250,
                            startX: 0,
                            startWidth: 0,
                            startResize(e) {
                                this.startX = e.pageX;
                                this.startWidth = this.$el.offsetWidth;
                                const onMouseMove = (e) => {
                                    this.width = Math.max(100, this.startWidth + (e.pageX - this.startX));
                                };
                                const onMouseUp = () => {
                                    document.removeEventListener('mousemove', onMouseMove);
                                    document.removeEventListener('mouseup', onMouseUp);
                                    document.body.style.cursor = 'default';
                                };
                                document.body.style.cursor = 'col-resize';
                                document.addEventListener('mousemove', onMouseMove);
                                document.addEventListener('mouseup', onMouseUp);
                            }
                        }"
                        :style="`width: ${width}px; min-width: ${width}px; max-width: ${width}px`"
                    >
                        <div class="flex items-center justify-center gap-1 overflow-hidden text-ellipsis">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            AVIS CLIENT
                        </div>
                        <div class="absolute right-0 top-0 bottom-0 z-20"
                             style="width: 10px; cursor: col-resize; transform: translateX(5px); background: transparent;"
                             onmouseover="this.style.background='rgba(74, 222, 128, 0.5)'"
                             onmouseout="this.style.background='transparent'"
                             @mousedown.prevent="startResize"></div>
                    </th>
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
                                // Dynamic color from option_colors, fallback to keyword-based
                                $inlineStyle = '';
                                $badgeClass = '';
                                if (!empty($field['option_colors'][$val])) {
                                    $hex = $field['option_colors'][$val];
                                    $inlineStyle = "background-color: {$hex}22; color: {$hex};";
                                } elseif ($field['name'] === 'status' || $field['name'] === 'etat_test') {
                                    if (in_array(strtolower($val), ['validé', 'terminé'])) $badgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
                                    elseif (in_array(strtolower($val), ['optimisation', 'en cours'])) $badgeClass = 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300';
                                    elseif (in_array(strtolower($val), ['sous réserve', 'non validé', 'échec'])) $badgeClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                                }
                            @endphp

                                @php
                                    $isTester = auth()->check() && auth()->user()->hasRole('tester');
                                    $isDev = auth()->check() && auth()->user()->hasRole('developer');
                                    $isClient = auth()->check() && auth()->user()->hasRole('client');
                                    $isChef = auth()->check() && auth()->user()->hasRole('chef_project');
                                    
                                    // Mots-clés qui rendent une colonne modifiable par le testeur ou le client
                                    $testerEditableKeywords = ['etat', 'status', 'statut', 'result', 'nature', 'comment'];
                                    $clientEditableKeywords = ['client', 'uat', 'retour_client', 'validation'];
                                    
                                    $isReadOnly = false;
                                    if ($isTester) {
                                        $isEditableForTester = false;
                                        foreach ($testerEditableKeywords as $keyword) {
                                            if (str_contains(strtolower($field['name']), $keyword)) {
                                                $isEditableForTester = true;
                                                break;
                                            }
                                        }
                                        $isReadOnly = !$isEditableForTester;
                                    } elseif ($isDev) {
                                        $isReadOnly = !str_contains(strtolower($field['name']), 'retour_dev');
                                    } elseif ($isClient) {
                                        $isEditableForClient = false;
                                        foreach ($clientEditableKeywords as $keyword) {
                                            if (str_contains(strtolower($field['name']), $keyword)) {
                                                $isEditableForClient = true;
                                                break;
                                            }
                                        }
                                        $isReadOnly = !$isEditableForClient;
                                    } elseif ($isChef) {
                                        if (str_contains(strtolower($field['name']), 'retour_dev')) {
                                            $isReadOnly = true;
                                        }
                                    }
                                    
                                    $allowImageUpload = false;
                                    if (!$isReadOnly) {
                                        if ($isTester && str_contains(strtolower($field['name']), 'comment')) $allowImageUpload = true;
                                        if ($isDev && str_contains(strtolower($field['name']), 'retour_dev')) $allowImageUpload = true;
                                        if ($isChef && str_contains(strtolower($field['name']), 'comment')) $allowImageUpload = true;
                                    }

                                    preg_match_all('/!\[capture\]\(([^)]+)\)/', $val, $imgMatches);
                                    $cleanText = trim(preg_replace('/\n?!\[capture\]\([^)]+\)/', '', $val));
                                    $existingImagesJson = json_encode($imgMatches[1] ?? []);

                                    $cellBgClass = $badgeClass ?: ($isReadOnly ? 'bg-gray-200 dark:bg-gray-800 cursor-not-allowed opacity-80' : 'bg-white dark:bg-gray-900');
                                @endphp
                                
                                <td class="p-0 border-r border-gray-200 dark:border-gray-700 {{ $cellBgClass }} relative transition-colors" @if($inlineStyle) style="{{ $inlineStyle }}" @endif>
                                    @if($isReadOnly)
                                        <div class="w-full h-full min-h-[40px] px-3 py-2 text-gray-500 dark:text-gray-400 {{ $field['type'] === 'textarea' ? 'whitespace-pre-wrap' : '' }}">
                                            @if($field['type'] === 'url' && $val)
                                                <a href="{{ $val }}" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1">
                                                    {{ $val }}
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                {{ $cleanText }}
                                                @if(!empty($imgMatches[1]))
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach($imgMatches[1] as $imgUrl)
                                                        <a href="{{ $imgUrl }}" target="_blank">
                                                            <img src="{{ $imgUrl }}" class="h-6 w-6 object-cover rounded border border-gray-300 dark:border-gray-600 shadow-sm hover:opacity-80 transition" alt="capture">
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                @endif
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
                                        class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none appearance-none {{ (!empty($field['option_colors'][$val]) || $badgeClass) ? 'font-semibold' : '' }}"
                                        style="{{ $inlineStyle }}"
                                    >
                                        <option value=""></option>
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option }}" @selected($val === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field['type'] === 'textarea')
                                    @if(!$isReadOnly)
                                        @if($allowImageUpload)
                                        {{-- Textarea éditable avec upload image --}}
                                        <div
                                            x-data="cellImageUpload({{ $row->id }}, '{{ $field['name'] }}', {{ $existingImagesJson }})"
                                            class="relative w-full h-full min-h-[40px]"
                                        >
                                            <textarea
                                                x-ref="textarea"
                                                @blur="saveCell()"
                                                @paste="handlePaste($event)"
                                                class="w-full h-full min-h-[40px] px-3 py-2 pb-8 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none resize-none overflow-hidden"
                                                rows="1"
                                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                            >{{ $cleanText }}</textarea>
                                            {{-- Barre basse avec bouton image --}}
                                            <div class="absolute bottom-1 left-1 flex items-center gap-1">
                                                <label
                                                    class="flex items-center gap-1 px-1.5 py-0.5 rounded cursor-pointer text-[10px] font-medium transition"
                                                    :class="uploading ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500'"
                                                    title="Joindre une capture d'écran ou coller avec Ctrl+V"
                                                >
                                                    <svg x-show="!uploading" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <svg x-show="uploading" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                    <input type="file" accept="image/*" class="hidden" @change="uploadFile($event)" :disabled="uploading">
                                                </label>
                                                <template x-for="(imgUrl, index) in images" :key="index">
                                                    <div class="relative group">
                                                        <a :href="imgUrl" target="_blank" class="flex-shrink-0 block">
                                                            <img :src="imgUrl" class="h-6 w-6 object-cover rounded border border-gray-300 hover:opacity-80 transition" alt="capture">
                                                        </a>
                                                        <button type="button" @click.stop="removeImage(index)" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-3 h-3 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
                                                            <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        @else
                                        {{-- Textarea classique --}}
                                        <textarea
                                            wire:blur="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                            class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none resize-none overflow-hidden"
                                            rows="1"
                                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                        >{{ $cleanText }}</textarea>
                                        @if(count($imgMatches[1] ?? []) > 0)
                                        <div class="px-3 pb-2 flex gap-1">
                                            @foreach($imgMatches[1] as $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="block">
                                                    <img src="{{ $imgUrl }}" class="h-5 w-5 object-cover rounded border border-gray-200 hover:opacity-80 transition" title="capture">
                                                </a>
                                            @endforeach
                                        </div>
                                        @endif
                                        @endif
                                    @else
                                    {{-- Textarea read-only : affiche le texte + miniatures si images intégrées --}}
                                    <div class="w-full min-h-[40px] px-3 py-2 text-gray-500 dark:text-gray-400 whitespace-pre-wrap text-sm">
                                        {{ $cleanText }}
                                        @if(count($imgMatches[1] ?? []) > 0)
                                            <div class="mt-2 flex gap-2 flex-wrap">
                                            @foreach($imgMatches[1] as $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="block">
                                                    <img src="{{ $imgUrl }}" class="h-16 rounded border border-gray-200 hover:opacity-80 transition shadow-sm" alt="capture">
                                                </a>
                                            @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                @else
                                    @if(!$isReadOnly)
                                        @if($allowImageUpload)
                                        {{-- Input text éditable avec upload image --}}
                                        <div
                                            x-data="cellImageUpload({{ $row->id }}, '{{ $field['name'] }}', {{ $existingImagesJson }})"
                                            class="relative flex items-center w-full h-full min-h-[40px]"
                                        >
                                            <input
                                                x-ref="textarea"
                                                type="text"
                                                value="{{ $cleanText }}"
                                                @blur="saveCell()"
                                                @paste="handlePaste($event)"
                                                class="flex-1 h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none"
                                            >
                                            <label
                                                class="flex-shrink-0 p-1 cursor-pointer text-gray-300 hover:text-blue-400 transition mr-1"
                                                :class="uploading ? 'text-blue-400' : ''"
                                                title="Joindre une capture d'écran"
                                            >
                                                <svg x-show="!uploading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <svg x-show="uploading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                <input type="file" accept="image/*" class="hidden" @change="uploadFile($event)" :disabled="uploading">
                                            </label>
                                            <template x-for="(imgUrl, index) in images" :key="index">
                                                <div class="relative group mr-1">
                                                    <a :href="imgUrl" target="_blank">
                                                        <img :src="imgUrl" class="h-5 w-5 object-cover rounded border border-gray-300 hover:opacity-80 transition flex-shrink-0" title="capture">
                                                    </a>
                                                    <button type="button" @click.stop="removeImage(index)" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-3 h-3 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
                                                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        @else
                                        {{-- Input text classique --}}
                                        <input
                                            type="text"
                                            value="{{ $cleanText }}"
                                            wire:blur="updateCell({{ $row->id }}, '{{ $field['name'] }}', $event.target.value)"
                                            class="w-full h-full min-h-[40px] px-3 py-2 bg-transparent border-none focus:ring-2 focus:ring-[#8b0000] focus:bg-white dark:focus:bg-gray-700 outline-none"
                                        >
                                        @if(count($imgMatches[1] ?? []) > 0)
                                        <div class="px-3 pb-2 flex gap-1">
                                            @foreach($imgMatches[1] as $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="block">
                                                    <img src="{{ $imgUrl }}" class="h-5 w-5 object-cover rounded border border-gray-200 hover:opacity-80 transition" title="capture">
                                                </a>
                                            @endforeach
                                        </div>
                                        @endif
                                        @endif
                                    @else
                                    {{-- Input text read-only --}}
                                    <div class="w-full h-full min-h-[40px] px-3 py-2 text-gray-500 dark:text-gray-400">
                                        {{ $cleanText }}
                                        @if(count($imgMatches[1] ?? []) > 0)
                                            <div class="mt-2 flex gap-2 flex-wrap">
                                            @foreach($imgMatches[1] as $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="block">
                                                    <img src="{{ $imgUrl }}" class="h-16 rounded border border-gray-200 hover:opacity-80 transition shadow-sm" alt="capture">
                                                </a>
                                            @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        
                        @if(auth()->check() && auth()->user()->hasRole('chef_project'))
                        <td class="px-2 py-2 text-center">
                            <button wire:click="deleteRow({{ $row->id }})" wire:confirm="Supprimer cette ligne ?" class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                        @endif

                        {{-- Colonne AVIS CLIENT --}}
                        @if(auth()->check() && (auth()->user()->hasRole('client') || auth()->user()->hasRole('chef_project')))
                        <td class="px-3 py-2 border-l-2 border-purple-200 dark:border-purple-700 bg-purple-50/30 dark:bg-purple-900/10">
                            @php
                                $clientStatus = $row->client_status ?? 'pending';
                                $statusConfig = match($clientStatus) {
                                    'validated' => ['label' => 'Validé',    'class' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',  'icon' => '✅'],
                                    'rejected'  => ['label' => 'Rejeté',   'class' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',    'icon' => '❌'],
                                    default     => ['label' => 'En attente','class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'icon' => '⏳'],
                                };
                            @endphp
                            <div class="flex flex-col gap-2 min-w-[140px]">
                                {{-- Badge statut --}}
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['icon'] }} {{ $statusConfig['label'] }}
                                </span>

                                {{-- Commentaire du client (visible chef) --}}
                                @if($row->client_comment)
                                <div class="text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 rounded p-1.5 border border-gray-200 dark:border-gray-600 max-w-[200px]">
                                    @php
                                        preg_match_all('/!\[capture\]\(([^)]+)\)/', $row->client_comment, $cm);
                                        $cleanComment = preg_replace('/\n?!\[capture\]\([^)]+\)/', '', $row->client_comment);
                                    @endphp
                                    <p class="line-clamp-3">{{ $cleanComment }}</p>
                                    @foreach($cm[1] as $cUrl)
                                    <a href="{{ $cUrl }}" target="_blank" class="mt-1 block">
                                        <img src="{{ $cUrl }}" class="h-6 rounded border border-gray-200 hover:opacity-80 transition" alt="capture client">
                                    </a>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Boutons d'action (client uniquement) --}}
                                @if(auth()->user()->hasRole('client'))
                                <div class="flex gap-1">
                                    @if($clientStatus !== 'validated')
                                    <button
                                        wire:click="validateCase({{ $row->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex items-center gap-1 px-2 py-1 rounded-md bg-green-600 hover:bg-green-700 text-white text-[11px] font-semibold transition shadow-sm"
                                        title="Valider ce cas de test"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        OK
                                    </button>
                                    @endif
                                    @if($clientStatus !== 'rejected')
                                    <button
                                        wire:click="openRejectModal({{ $row->id }})"
                                        class="flex items-center gap-1 px-2 py-1 rounded-md bg-red-600 hover:bg-red-700 text-white text-[11px] font-semibold transition shadow-sm"
                                        title="Rejeter ce cas de test"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        KO
                                    </button>
                                    @endif
                                    @if($clientStatus !== 'pending')
                                    <button
                                        wire:click="validateCase({{ $row->id }})" {{-- reset via revalider --}}
                                        class="px-2 py-1 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-[10px] transition"
                                        title="Réinitialiser l'avis"
                                    >↺</button>
                                    @endif
                                </div>
                                @endif
                            </div>
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
            min-width: 100px;
        }
        table th {
            letter-spacing: 0.05em;
        }
    </style>

    {{-- Alpine.js : upload image dans les cellules (testeur, développeur, chef, client) --}}
    <script>
    function cellImageUpload(rowId, fieldName, existingImages = []) {
        return {
            uploading: false,
            images: existingImages,

            async handlePaste(event) {
                const items = event.clipboardData?.items;
                if (!items) return;
                for (const item of items) {
                    if (item.type.startsWith('image/')) {
                        event.preventDefault();
                        await this.doUpload(item.getAsFile());
                        return;
                    }
                }
            },

            async uploadFile(event) {
                const file = event.target.files[0];
                if (file) await this.doUpload(file);
                event.target.value = '';
            },

            async doUpload(file) {
                this.uploading = true;
                try {
                    const fd = new FormData();
                    fd.append('image', file);
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    const res = await fetch('/upload-image', { method: 'POST', body: fd });
                    if (!res.ok) { this.uploading = false; return; }
                    const data = await res.json();
                    if (data.url) {
                        this.images.push(data.url);
                        this.saveCell();
                    }
                } catch(e) { console.error('Upload error', e); }
                this.uploading = false;
            },

            removeImage(index) {
                this.images.splice(index, 1);
                this.saveCell();
            },

            saveCell() {
                const el = this.$refs.textarea;
                if (!el) return;
                const textValue = el.value.trim();
                const mdImages = this.images.map(url => `![capture](${url})`).join('\n');
                
                // On assemble le texte et les images (avec un saut de ligne si on a les deux)
                let finalValue = textValue;
                if (mdImages) {
                    finalValue = textValue ? textValue + '\n\n' + mdImages : mdImages;
                }
                
                this.$wire.updateCell(rowId, fieldName, finalValue);
            }
        };
    }
    </script>

    <!-- Modal Gestion des Colonnes -->
    @if($showColumnModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
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
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-500">Type:</span>
                                            <select wire:change="updateColumnType('{{ $field['name'] }}', $event.target.value)" class="text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded px-1 py-0.5 focus:outline-none focus:ring-1 focus:ring-[#8b0000]">
                                                <option value="text" @if($field['type'] === 'text') selected @endif>Texte court</option>
                                                <option value="textarea" @if($field['type'] === 'textarea') selected @endif>Texte long</option>
                                                <option value="select" @if($field['type'] === 'select') selected @endif>Menu déroulant</option>
                                                <option value="url" @if($field['type'] === 'url') selected @endif>Lien URL</option>
                                            </select>
                                            @if($field['type'] === 'select')
                                            <button type="button" wire:click="openOptionsEditor('{{ $field['name'] }}')" class="text-xs text-[#8b0000] hover:text-red-800 flex items-center gap-1 font-medium px-2 py-0.5 border border-[#8b0000] rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                                Options
                                            </button>
                                            @endif
                                        </div>
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

    <!-- Modal Éditeur d'Options (Color Picker) -->
    @if($showOptionsEditor)
    <div class="fixed inset-0 z-[69] bg-gray-900 bg-opacity-75" wire:click="closeOptionsEditor"></div>
    <div class="fixed inset-0 z-[70] overflow-y-auto pointer-events-none" aria-labelledby="options-editor-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="pointer-events-auto relative bg-white dark:bg-gray-800 rounded-xl text-left shadow-xl w-full max-w-xl border border-gray-200 dark:border-gray-700 my-8">
                <div class="px-6 pt-5 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="options-editor-title">
                            Options du menu — <span class="text-[#8b0000]">{{ $editingOptionsColumn }}</span>
                        </h3>
                        <button wire:click="closeOptionsEditor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Définissez les options disponibles et choisissez librement la couleur d'affichage de chaque option dans la grille.</p>

                    <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                        @foreach($editingOptions as $i => $opt)
                        <div class="flex items-center gap-3">
                            <!-- Color picker -->
                            <div class="flex-shrink-0">
                                <label class="text-xs text-gray-500 block mb-1 text-center">Couleur</label>
                                <input
                                    type="color"
                                    wire:model.live="editingOptions.{{ $i }}.color"
                                    value="{{ $opt['color'] }}"
                                    class="w-10 h-10 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer p-0.5 bg-white"
                                    title="Choisir une couleur"
                                >
                            </div>
                            <!-- Preview badge -->
                            <div class="flex-shrink-0 w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $opt['color'] }}"></div>
                            <!-- Text input -->
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 block mb-1">Libellé de l'option</label>
                                <input
                                    type="text"
                                    wire:model="editingOptions.{{ $i }}.value"
                                    placeholder="Ex: Validé, En cours..."
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]"
                                >
                            </div>
                            <!-- Remove button -->
                            <button type="button" wire:click="removeOption({{ $i }})" class="flex-shrink-0 mt-5 p-2 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addOption" class="mt-4 flex items-center gap-2 text-sm text-[#8b0000] hover:text-red-800 font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajouter une option
                    </button>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeOptionsEditor" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="button" wire:click="saveOptions" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#8b0000] text-sm font-medium text-white hover:bg-red-800 transition">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Import Excel -->
    @if($showImportModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
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

    {{-- Modal Commit Session (Testeur) --}}
    @if($showCommitModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-10 px-4 pb-24 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showCommitModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">Soumettre votre session de tests</h3>
                    <p class="text-sm text-gray-500 mb-4">Décrivez ce que vous avez testé ou trouvé. Vous pouvez joindre une capture d'écran (📷) ou coller une image avec Ctrl+V.</p>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message de validation (obligatoire)</label>
                        <x-rich-textarea
                            wire-model="commitMessage"
                            placeholder="Ex: Tests d'inscription terminés. 2 bugs mineurs trouvés..."
                            :rows="4"
                        />
                        @error('commitMessage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showCommitModal', false)" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 sm:text-sm">Annuler</button>
                    <button type="button" wire:click="commitSession" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:text-sm">Soumettre au journal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Rejet Client UAT --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="reject-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pb-24 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity" aria-hidden="true"></div>
            <div class="relative z-10 inline-block bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg border border-red-200 dark:border-red-800">

                {{-- En-tête modal --}}
                <div class="px-6 pt-5 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white" id="reject-modal-title">Signaler un problème</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ce cas de test sera marqué comme <strong>rejeté</strong> et le chef de projet sera notifié.</p>
                        </div>
                    </div>
                </div>

                {{-- Résumé du cas --}}
                @if($rejectCaseSummary)
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cas de test concerné</p>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-medium line-clamp-2">{{ $rejectCaseSummary }}</p>
                </div>
                @endif

                {{-- Formulaire --}}
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Décrivez le problème constaté <span class="text-red-500">*</span>
                    </label>
                    <x-rich-textarea
                        wire-model="rejectComment"
                        placeholder="Décrivez le problème : ce qui ne fonctionne pas, ce que vous attendiez... Vous pouvez coller une capture d'écran avec Ctrl+V."
                        :rows="5"
                        id="reject-comment-textarea"
                    />
                    @error('rejectComment')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="cancelRejection"
                        class="inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        wire:click="submitRejection"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 bg-red-600 hover:bg-red-700 text-sm font-semibold text-white transition shadow-sm disabled:opacity-50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Soumettre le rejet
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>