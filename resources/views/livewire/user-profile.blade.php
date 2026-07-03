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
                                <img class="h-20 w-20 object-cover rounded-full ring-4 ring-gray-50 dark:ring-gray-700" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->must_change_password ? 'Vous devez d\'abord saisir votre mot de passe temporaire reçu par email avant de définir un nouveau mot de passe.' : 'Assurez-vous d\'utiliser un mot de passe long et sécurisé.' }}</p>
                
                <form wire:submit="updatePassword" class="mt-6 space-y-6">
                    <div class="max-w-md space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe actuel</label>
                            <div class="relative mt-1">
                                <input id="current_password" type="password" wire:model="current_password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-password-toggle data-target="current_password" aria-label="Afficher ou masquer le mot de passe">
                                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.152-3.292m3.36-2.36A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('current_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        @if (auth()->user()->must_change_password)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe temporaire reçu par email</label>
                                <div class="relative mt-1">
                                    <input id="temporary_password" type="password" wire:model="temporary_password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-password-toggle data-target="temporary_password" aria-label="Afficher ou masquer le mot de passe temporaire">
                                        <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.152-3.292m3.36-2.36A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                                    </button>
                                </div>
                                @error('temporary_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau mot de passe</label>
                            <div class="relative mt-1">
                                <input id="password" type="password" wire:model="password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-password-toggle data-target="password" aria-label="Afficher ou masquer le nouveau mot de passe">
                                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.152-3.292m3.36-2.36A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmer le nouveau mot de passe</label>
                            <div class="relative mt-1">
                                <input id="password_confirmation" type="password" wire:model="password_confirmation" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-password-toggle data-target="password_confirmation" aria-label="Afficher ou masquer la confirmation du mot de passe">
                                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.152-3.292m3.36-2.36A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-start pt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                            button.addEventListener('click', function () {
                                const target = document.getElementById(button.getAttribute('data-target'));
                                if (!target) {
                                    return;
                                }

                                const isPassword = target.type === 'password';
                                target.type = isPassword ? 'text' : 'password';
                                button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

                                const eyeOpen = button.querySelector('[data-eye-open]');
                                const eyeClosed = button.querySelector('[data-eye-closed]');
                                if (eyeOpen && eyeClosed) {
                                    eyeOpen.classList.toggle('hidden', isPassword);
                                    eyeClosed.classList.toggle('hidden', !isPassword);
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
