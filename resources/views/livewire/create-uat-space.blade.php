<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Créer un espace UAT</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Générez un espace isolé pour le client afin qu'il puisse exécuter les tests du projet <strong>{{ $project?->name }}</strong>.
                    </p>
                    
                    @if($generatedLink)
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">
                            <h4 class="text-green-800 font-semibold mb-2">Espace UAT créé avec succès !</h4>
                            <div class="space-y-2 text-sm text-green-700">
                                <p><strong>Lien d'accès :</strong> <a href="{{ $generatedLink }}" class="underline" target="_blank">{{ $generatedLink }}</a></p>
                                <p><strong>Email :</strong> {{ $clientEmail }}</p>
                                <p><strong>Mot de passe :</strong> <span class="bg-white px-2 py-1 rounded font-mono">{{ $generatedPassword }}</span></p>
                            </div>
                            <p class="text-xs text-green-600 mt-3">Communiquez ces informations au client pour qu'il puisse se connecter.</p>
                        </div>
                    @else
                        <form wire:submit="createSpace" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du client / de l'entreprise</label>
                                <input type="text" wire:model="clientName" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                @error('clientName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email de contact</label>
                                <input type="email" wire:model="clientEmail" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                                @error('clientEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#8b0000] text-base font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:text-sm">
                                    Générer l'espace UAT
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
                
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8b0000] sm:mt-0 sm:w-auto sm:text-sm">
                        {{ $generatedLink ? 'Fermer' : 'Annuler' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
