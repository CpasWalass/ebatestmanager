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
