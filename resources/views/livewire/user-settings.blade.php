<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Paramètres</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Gérez vos préférences de sécurité et vos sessions actives.</p>
    </div>

    @if (session()->has('success_sessions'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg shadow-sm">
            {{ session('success_sessions') }}
        </div>
    @endif

    <div class="space-y-8">
        {{-- Apparence --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Apparence</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Personnalisez l'apparence de l'application.</p>

                <div class="mt-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Mode sombre</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Activez ou désactivez le thème sombre.</p>
                    </div>
                    <button @click="toggle()" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="dark ? 'bg-[#8b0000]' : 'bg-gray-300'">
                        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-white transition-transform" :class="dark ? 'translate-x-6' : 'translate-x-1'">
                            <svg x-show="!dark" class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"/>
                            </svg>
                            <svg x-show="dark" class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sessions actives --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Sessions du navigateur</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gérez et déconnectez vos sessions actives sur d'autres navigateurs et appareils.</p>
                
                <div class="mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Si nécessaire, vous pouvez vous déconnecter de toutes vos autres sessions de navigateur sur l'ensemble de vos appareils. Si vous pensez que votre compte a été compromis, vous devriez également mettre à jour votre mot de passe.
                    </p>

                    @if (count($sessions) > 0)
                        <div class="mt-5 space-y-6">
                            @foreach ($sessions as $session)
                                <div class="flex items-center">
                                    <div>
                                        @if ($session->agent->is_desktop)
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>

                                    <div class="ml-3">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $session->agent->platform ? $session->agent->platform : 'Inconnu' }} - {{ $session->agent->browser ? $session->agent->browser : 'Inconnu' }}
                                        </div>

                                        <div>
                                            <div class="text-xs text-gray-500">
                                                {{ $session->ip_address }},

                                                @if ($session->is_current_device)
                                                    <span class="text-green-500 font-semibold">Cet appareil</span>
                                                @else
                                                    Dernière activité {{ $session->last_active }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center mt-6">
                        <button wire:click="logoutOtherBrowserSessions" wire:confirm="Voulez-vous vraiment déconnecter vos autres sessions ?" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Déconnecter les autres sessions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
