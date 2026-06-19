<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profil Utilisateur</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Gérez vos informations personnelles et votre sécurité.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('success_password'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg shadow-sm">
            {{ session('success_password') }}
        </div>
    @endif

    <div class="space-y-8">
        {{-- Informations du profil --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Informations Personnelles</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mettez à jour votre nom, votre adresse email et votre photo de profil.</p>
                
                <form wire:submit="updateProfile" class="mt-6 space-y-6">
                    {{-- Avatar --}}
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if ($avatar)
                                <img class="h-20 w-20 object-cover rounded-full ring-4 ring-gray-50 dark:ring-gray-700" src="{{ $avatar->temporaryUrl() }}" alt="Aperçu Avatar">
                            @elseif (auth()->user()->avatar)
                                <img class="h-20 w-20 object-cover rounded-full ring-4 ring-gray-50 dark:ring-gray-700" src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar">
                            @else
                                <div class="h-20 w-20 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl font-bold ring-4 ring-gray-50 dark:ring-gray-700">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <label class="block">
                            <span class="sr-only">Choisir une photo de profil</span>
                            <input type="file" wire:model="avatar" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                dark:file:bg-gray-700 dark:file:text-gray-300
                                hover:file:bg-blue-100 dark:hover:file:bg-gray-600
                                cursor-pointer
                            "/>
                            <div wire:loading wire:target="avatar" class="mt-2 text-sm text-blue-600">Chargement...</div>
                        </label>
                    </div>
                    @error('avatar') <span class="text-sm text-red-600">{{ $message }}</span> @enderror

                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom complet</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse Email</label>
                            <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mot de passe --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Changer de mot de passe</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assurez-vous d'utiliser un mot de passe long et sécurisé.</p>
                
                <form wire:submit="updatePassword" class="mt-6 space-y-6">
                    <div class="max-w-md space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe actuel</label>
                            <input type="password" wire:model="current_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('current_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau mot de passe</label>
                            <input type="password" wire:model="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmer le nouveau mot de passe</label>
                            <input type="password" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex justify-start pt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
