<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Proposer un nouveau test UAT</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Exprimez vos besoins métiers pour la recette</p>
    </div>

    <form wire:submit.prevent="submit" class="p-6 space-y-5">
        
        @if($showSuccessMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="p-4 mb-4 text-sm text-green-700 bg-green-50 rounded-xl border border-green-200">
            ✅ Le cas de test a été soumis avec succès à l'équipe.
        </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Projet concerné</label>
            <select wire:model="projectId" class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:border-transparent transition">
                <option value="">Sélectionner un projet...</option>
                @foreach($projets as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            @error('projectId') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Titre / Objectif</label>
            <input type="text" wire:model="titre" placeholder="Ex: Validation de la création d'un compte" class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:border-transparent transition">
            @error('titre') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Scénario Utilisateur (Ce que je fais)</label>
            <textarea wire:model="scenario" rows="3" placeholder="En tant que client, je clique sur... puis je remplis le formulaire..." class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:border-transparent resize-none transition"></textarea>
            @error('scenario') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Résultat Attendu (Ce que j'attends)</label>
            <textarea wire:model="resultat" rows="3" placeholder="Le système doit afficher un message de succès et envoyer un email..." class="w-full text-sm px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:border-transparent resize-none transition"></textarea>
            @error('resultat') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-white font-medium transition" style="background:#8b0000;" onmouseover="this.style.background='#660000'" onmouseout="this.style.background='#8b0000'">
                <svg wire:loading class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove>Soumettre le Test UAT</span>
                <span wire:loading>Traitement...</span>
            </button>
        </div>
    </form>
</div>
