{{--
    Composant <x-rich-textarea>
    Textarea enrichi avec upload d'image optionnel.

    @param string $wireModel     Propriété Livewire (ex: "rejectComment")
    @param string $placeholder   Texte indicatif
    @param int    $rows          Nombre de lignes (défaut: 4)
    @param string $id            ID unique du champ (généré automatiquement si absent)
--}}
@props([
    'wireModel'   => '',
    'placeholder' => 'Écrivez votre commentaire...',
    'rows'        => 4,
    'id'          => null,
])

<div
    x-data="{
        uploading: false,
        uploadedImages: [],
        uploadError: null,
        charCount: 0,

        init() {
            const ta = this.$refs.textarea;
            if (ta) {
                // Initialiser avec la valeur existante depuis Livewire
                const wireModelAttr = ta.getAttribute('wire:model') || ta.getAttribute('wire:model.defer') || ta.getAttribute('wire:model.live');
                if (this.$wire && wireModelAttr) {
                    let val = this.$wire.get(wireModelAttr) || '';
                    
                    // Extraire les images existantes
                    const regex = /!\[capture\]\(([^)]+)\)/g;
                    let match;
                    while ((match = regex.exec(val)) !== null) {
                        this.uploadedImages.push({ url: match[1], name: 'capture' });
                    }
                    
                    // Nettoyer le texte visible
                    ta.value = val.replace(/\n?!\[capture\]\([^)]+\)/g, '').trim();
                }

                this.charCount = ta.value.length;
                ta.addEventListener('input', () => { 
                    this.charCount = ta.value.length; 
                    this.syncToLivewire();
                });
            }
        },

        syncToLivewire() {
            const ta = this.$refs.textarea;
            const wireModelAttr = ta.getAttribute('wire:model') || ta.getAttribute('wire:model.defer') || ta.getAttribute('wire:model.live');
            if (this.$wire && wireModelAttr) {
                const mdImages = this.uploadedImages.map(img => `![capture](${img.url})`).join('\n');
                let finalValue = ta.value;
                if (mdImages) finalValue = finalValue ? finalValue + '\n\n' + mdImages : mdImages;
                this.$wire.set(wireModelAttr, finalValue);
            }
        },

        async handlePaste(event) {
            const items = event.clipboardData?.items;
            if (!items) return;
            for (const item of items) {
                if (item.type.startsWith('image/')) {
                    event.preventDefault();
                    const file = item.getAsFile();
                    await this.uploadFile(file);
                    return;
                }
            }
        },

        async uploadImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            await this.uploadFile(file);
            event.target.value = '';
        },

        async uploadFile(file) {
            this.uploading = true;
            this.uploadError = null;

            try {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('meta[name=\'csrf-token\']').content);

                const response = await fetch('/upload-image', {
                    method: 'POST',
                    body: formData,
                });

                if (!response.ok) {
                    const err = await response.json();
                    this.uploadError = err.message || 'Erreur lors de l\'upload.';
                    return;
                }

                const data = await response.json();
                if (data.url) {
                    this.uploadedImages.push({ url: data.url, name: data.name });
                    this.syncToLivewire();
                }
            } catch (e) {
                this.uploadError = 'Erreur réseau lors de l\'upload.';
            } finally {
                this.uploading = false;
            }
        },

        removeImage(idx) {
            this.uploadedImages.splice(idx, 1);
            this.syncToLivewire();
        }
    }"
    class="relative"
>
    {{-- Zone de texte principale --}}
    <textarea
        id="{{ $id }}"
        x-ref="textarea"
        data-rich-clean="true"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @paste="handlePaste($event)"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->class([
            'w-full rounded-lg border border-gray-300 dark:border-gray-600',
            'bg-white dark:bg-gray-700 text-gray-900 dark:text-white',
            'px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]',
            'resize-y transition pb-9',
        ]) }}
    ></textarea>

    {{-- Barre d'outils basse --}}
    <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-2 pointer-events-auto">
            {{-- Bouton camera --}}
            <label
                :title="uploading ? 'Envoi en cours...' : 'Joindre une capture d\'écran'"
                class="flex items-center gap-1 px-2 py-1 rounded-md cursor-pointer text-xs font-medium transition"
                :class="uploading
                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 cursor-wait'
                    : 'bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-400'"
            >
                <svg x-show="!uploading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <svg x-show="uploading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="uploading ? 'Envoi...' : 'Image'"></span>
                <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="uploadImage($event)"
                    :disabled="uploading"
                >
            </label>

            {{-- Message d'aide --}}
            <span class="text-xs text-gray-400 dark:text-gray-500">
                ou collez une image (Ctrl+V)
            </span>
        </div>

        {{-- Compteur de caractères --}}
        <span class="text-xs text-gray-400 dark:text-gray-500 pointer-events-none" x-text="charCount + ' car.'"></span>
    </div>

    {{-- Aperçus des images uploadées --}}
    <template x-if="uploadedImages.length > 0">
        <div class="mt-2 flex flex-wrap gap-2">
            <template x-for="(img, idx) in uploadedImages" :key="idx">
                <div class="relative group">
                    <a :href="img.url" target="_blank">
                        <img :src="img.url" :alt="img.name"
                            class="h-16 w-16 object-cover rounded-lg border-2 border-blue-200 dark:border-blue-800 shadow-sm hover:opacity-80 transition">
                    </a>
                    <button
                        type="button"
                        @click="removeImage(idx)"
                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-red-700"
                        title="Retirer l'image"
                    >×</button>
                </div>
            </template>
        </div>
    </template>

    {{-- Erreur upload --}}
    <template x-if="uploadError">
        <p class="mt-1 text-xs text-red-500" x-text="uploadError"></p>
    </template>
</div>

@once
@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el }) => {
            if (el instanceof HTMLTextAreaElement && el.dataset.richClean === 'true') {
                const cleaned = el.value.replace(/\n?!\[capture\]\([^)]+\)/g, '').trim();
                if (el.value !== cleaned) {
                    el.value = cleaned;
                }
            }
        });
    });
</script>
@endpush
@endonce
