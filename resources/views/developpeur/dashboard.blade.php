@extends('layouts.app')

@section('title', 'Tableau de Bord — Développeur')
@section('search_placeholder', 'Rechercher un rapport...')

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Rapports reçus et corrections à apporter
            </p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">En Attente</p>
                    <p class="text-3xl font-bold mt-1" style="color:#CC0000">{{ $enAttente }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rapports à traiter</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(204,0,0,0.1);">
                    <svg class="w-5 h-5" style="color:#CC0000" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Traités</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $traites }}</p>
                    <p class="text-xs text-gray-400 mt-1">Corrections soumises</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-green-50 dark:bg-green-900/20">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Mes Réponses</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $mesReponses->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">Corrections récentes</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-900/20">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-4-4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Projets en revue --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Projets en cours de correction</h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                {{ $projetsEnRevue->count() }} projet(s)
            </span>
        </div>

        @if($projetsEnRevue->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun projet en cours de correction</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($projetsEnRevue as $projet)
                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $projet->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Par: {{ $projet->creator?->name }} · {{ $projet->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('projets.index') }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition">
                            Voir le projet
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Rapports reçus --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Rapports Reçus</h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full" style="background:rgba(204,0,0,0.1); color:#CC0000;">
                {{ $rapportsRecus->count() }} rapport(s)
            </span>
        </div>

        @if($rapportsRecus->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun rapport reçu</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Les rapports des testeurs apparaîtront ici</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($rapportsRecus as $rapport)
                <div class="px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $rapport->type === 'iat' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                    {{ strtoupper($rapport->type) }}
                                </span>
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $rapport->title }}</h3>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Projet : <span class="font-medium">{{ $rapport->project?->name }}</span> ·
                                Par : {{ $rapport->creator?->name }} ·
                                {{ $rapport->created_at->format('d/m/Y') }}
                            </p>

                            @if($rapport->stats)
                            <div class="flex items-center gap-4 mt-3 text-xs">
                                <span class="flex items-center gap-1 text-green-600">✅ {{ $rapport->stats['valide'] ?? 0 }} succès</span>
                                <span class="flex items-center gap-1 text-red-600">💣 {{ $rapport->stats['non_valide'] ?? 0 }} échecs</span>
                                <span class="flex items-center gap-1 text-amber-600">🤔 {{ $rapport->stats['sous_reserve'] ?? 0 }} sous réserve</span>
                                <span class="flex items-center gap-1 text-blue-600">👷‍♂️ {{ $rapport->stats['optimisation'] ?? 0 }} optimisation</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 flex-shrink-0">
                            <button
                                onclick="document.getElementById('reply-{{ $rapport->id }}').classList.toggle('hidden')"
                                class="px-3 py-1.5 text-xs font-medium text-white rounded-lg transition"
                                style="background:#CC0000;"
                                onmouseover="this.style.background='#aa0000'"
                                onmouseout="this.style.background='#CC0000'">
                                Répondre
                            </button>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                    Télécharger
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-cloak
                                    class="absolute right-0 top-full mt-1 w-44 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden z-50">
                                    <a href="{{ route('rapports.export', ['report' => $rapport->id, 'format' => 'pdf']) }}" target="_blank"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-gray-600 transition">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        Exporter en PDF
                                    </a>
                                    <a href="{{ route('rapports.export', ['report' => $rapport->id, 'format' => 'word']) }}" target="_blank"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 transition border-t border-gray-100 dark:border-gray-600">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Exporter en Word
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Formulaire de réponse (masqué par défaut) --}}
                    <div id="reply-{{ $rapport->id }}" class="hidden mt-4">
                        <form action="{{ route('developpeur.rapports.reply') }}" method="POST" class="space-y-3"
                            x-data="devImageUpload('{{ $rapport->id }}')"
                            @submit="composeAndSubmit()">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $rapport->id }}">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Ce que j'ai corrigé / mes commentaires
                                </label>
                                <textarea name="content" rows="3"
                                    x-ref="devtextarea_{{ $rapport->id }}"
                                    @paste="await handlePaste($event)"
                                    class="w-full text-sm px-3 py-2 pb-8 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent resize-none"
                                    style="--tw-ring-color:#CC0000"
                                    placeholder="Décrivez les corrections apportées... (collez une image avec Ctrl+V)"></textarea>

                                {{-- Barre d'outils image --}}
                                <div class="flex items-center gap-2 mt-1">
                                    <label
                                        class="flex items-center gap-1.5 px-2 py-1 rounded-lg cursor-pointer text-xs font-medium transition"
                                        :class="uploading
                                            ? 'bg-blue-100 text-blue-600 cursor-wait'
                                            : 'bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 hover:bg-red-50 hover:text-[#CC0000]'"
                                        title="Joindre une capture d'écran"
                                    >
                                        <svg x-show="!uploading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg x-show="uploading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        <span x-text="uploading ? 'Envoi...' : 'Image'"></span>
                                        <input type="file" accept="image/*" class="hidden" @change="uploadFile($event)" :disabled="uploading">
                                    </label>

                                    <span class="text-xs text-gray-400 dark:text-gray-500">ou collez avec Ctrl+V</span>

                                    {{-- Miniatures des images uploadées --}}
                                    <div class="flex items-center gap-1 ml-auto">
                                        <template x-for="img in images" :key="img.url">
                                            <a :href="img.url" target="_blank">
                                                <img :src="img.url" class="h-7 w-7 object-cover rounded border border-gray-300 hover:opacity-80 transition" :title="img.name">
                                            </a>
                                        </template>
                                    </div>
                                </div>

                            </div>
                            <div class="flex items-center gap-3">
                                <button type="submit" class="px-4 py-2 text-xs font-medium text-white rounded-xl transition"
                                    style="background:#CC0000;"
                                    onmouseover="this.style.background='#aa0000'"
                                    onmouseout="this.style.background='#CC0000'">
                                    Envoyer la réponse
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('reply-{{ $rapport->id }}').classList.add('hidden')"
                                    class="px-4 py-2 text-xs font-medium rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    {{-- Mes Réponses --}}
    @if($mesReponses->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Mes dernières réponses</h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                {{ $mesReponses->count() }}
            </span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($mesReponses as $reponse)
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $reponse->report?->title }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Projet : {{ $reponse->report?->project?->name }} · Envoyé le {{ $reponse->created_at->format('d/m/Y à H:i') }}
                        </p>
                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                            @php
                                preg_match_all('/!\[capture\]\(([^)]+)\)/', $reponse->content, $rImgs);
                                $rText = preg_replace('/\n?!\[capture\]\([^)]+\)/', '', $reponse->content);
                            @endphp
                            <p class="whitespace-pre-wrap">{{ $rText }}</p>
                            @foreach($rImgs[1] as $rImg)
                            <a href="{{ $rImg }}" target="_blank" class="mt-2 block">
                                <img src="{{ $rImg }}" class="max-h-40 rounded-lg border border-gray-200 dark:border-gray-600 hover:opacity-80 transition shadow-sm" alt="capture développeur">
                            </a>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif


</div>

@push('scripts')
<script>
/**
 * Alpine.js — upload d'image dans le textarea de réponse dev (formulaires HTML classiques).
 * Les images sont affichées en miniatures, jamais en markdown brut dans le champ.
 * Le markdown ![capture](url) est reconstitué à la soumission du formulaire.
 */
function devImageUpload(rapportId) {
    return {
        uploading: false,
        images: [],

        async handlePaste(event) {
            const items = event.clipboardData?.items;
            if (!items) return;
            for (const item of items) {
                if (item.type.startsWith('image/')) {
                    event.preventDefault();
                    const file = item.getAsFile();
                    await this.doUpload(file);
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
                    this.images.push({ url: data.url, name: data.name });
                }
            } catch (e) {
                console.error('Upload error:', e);
            }
            this.uploading = false;
        },

        removeImage(idx) {
            this.images.splice(idx, 1);
        },

        composeAndSubmit() {
            const ta = document.querySelector(`[x-ref="devtextarea_${rapportId}"]`)
                    || document.getElementById(`devta-${rapportId}`);
            if (ta && this.images.length > 0) {
                const mdImages = this.images.map(img => `![capture](${img.url})`).join('\n');
                ta.value = (ta.value.trim() ? ta.value + '\n\n' : '') + mdImages;
            }
            return true;
        }
    };
}
</script>
@endpush

@endsection
