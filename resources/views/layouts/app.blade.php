<!DOCTYPE html>
<html lang="fr" x-data="darkMode()" :class="dark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EbaTestManager')</title>
    <meta name="description" content="@yield('meta_description', 'Plateforme de gestion des tests UAT/IAT - e-Business Afrique')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-inter" style="font-family: 'Inter', sans-serif;">

<div class="min-h-screen flex flex-col" style="padding-bottom: 110px;">

    {{-- ═══════════════════════════════════════ TOP NAVBAR ═══════════════════════════════════════ --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm" style="height:64px;">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-4">

            {{-- Logo & Marque --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0 bg-white border border-gray-200 overflow-hidden">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-full h-full object-cover" onerror="this.outerHTML='<span class=\'text-[#8b0000] font-bold text-sm tracking-tight\'>EB</span>'">
                </div>
                <div class="hidden sm:block leading-none">
                    <p class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">UAT/IAT Manager</p>
                    <p class="text-base font-bold text-[#8b0000]">e-Business Afrique</p>
                </div>
            </div>

            {{-- Barre de recherche centrée --}}
            <div class="flex-1 max-w-md mx-4 hidden md:block">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="global-search"
                        placeholder="@yield('search_placeholder', 'Rechercher un projet...')"
                        class="w-full pl-10 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="--tw-ring-color: #CC0000;">
                </div>
            </div>

            {{-- Actions droite --}}
            <div class="flex items-center gap-2 sm:gap-3">

                {{-- Badge multi-tenant --}}
                <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full border border-blue-100 dark:border-blue-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Multi-Tenant Active</span>
                </div>

                {{-- Notifications (opens messagerie for unread) --}}
                @auth
                @php $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count(); @endphp
                <button
                    onclick="Livewire.dispatch('openMessagerie')"
                    class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    id="btn-notifications"
                    title="Notifications"
                >
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 text-white text-xs rounded-full flex items-center justify-center font-bold" style="background:#CC0000; font-size:10px;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>

                {{-- Messagerie rapide --}}
                <button
                    onclick="Livewire.dispatch('openMessagerie')"
                    class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    id="btn-messages"
                    title="Messagerie"
                >
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-4-4z"/>
                    </svg>
                </button>
                @endauth

                {{-- Grille apps --}}
                <button class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition hidden sm:block" id="btn-apps">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>

                {{-- Dark mode --}}
                <button @click="toggle()" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="!dark" class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="dark" class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                {{-- Profil utilisateur --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                            style="background: linear-gradient(135deg, #CC0000, #ff4444);">
                            {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white leading-none">{{ auth()->user()?->name ?? 'Utilisateur' }}</p>
                            <p class="text-xs text-gray-400 capitalize leading-none mt-0.5">
                                @php
                                    $roleLabel = match(auth()->user()?->getRoleNames()->first()) {
                                        'chef_project' => 'Chef de Projet',
                                        'tester'       => 'Testeur',
                                        'developer'    => 'Développeur',
                                        'client'       => 'Client',
                                        default        => auth()->user()?->getRoleNames()->first() ?? 'Utilisateur',
                                    };
                                @endphp
                                {{ $roleLabel }}
                            </p>
                        </div>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false"
                        class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()?->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()?->email }}</p>
                        </div>
                        <a href="#" class="flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil
                        </a>
                        <a href="#" class="flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Paramètres
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- ══════════════════════════════════════ CONTENU PRINCIPAL ══════════════════════════════════════ --}}
    <main class="flex-1 mt-16 max-w-screen-2xl w-full mx-auto px-4 sm:px-6 py-6">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════ BOTTOM DOCK NAVIGATION ═══════════════════════════════════ --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 flex justify-center pointer-events-none">
        <div class="pointer-events-auto relative flex items-end justify-center gap-3 px-10 pt-8 pb-3"
            style="
                background: #1a1c23;
                border-radius: 50% 50% 0 0 / 100% 100% 0 0;
                box-shadow: 0 -10px 40px rgba(0,0,0,0.15);
                width: 600px;
                max-width: 95vw;
                border-top: 1px solid rgba(255,255,255,0.05);
            ">

            {{-- Items du dock filtrés par rôle --}}
            @php
                $user = auth()->user();
                $role = $user?->getRoleNames()->first() ?? '';
                $isChef = $role === 'chef_project';
                $isTester = $role === 'tester';
                $isClient = $role === 'client';
                $isDev = $role === 'developer';

                // Route du dashboard selon le rôle
                $dashRoute = match($role) {
                    'tester'    => 'testeur.dashboard',
                    'developer' => 'developpeur.dashboard',
                    'client'    => 'client.dashboard',
                    default     => 'dashboard',
                };

                // Route des projets selon le rôle
                $projetsRoute = match($role) {
                    'tester'    => 'testeur.projets.index',
                    'developer' => 'projets.index',
                    'client'    => 'projets.index',
                    default     => 'projets.index',
                };

                $dockItems = [];

                // Dashboard — visible par tous
                $dockItems[] = [
                    'id'     => 'dock-home',
                    'label'  => 'Accueil',
                    'route'  => $dashRoute,
                    'active' => request()->routeIs('dashboard') || request()->routeIs('testeur.dashboard') || request()->routeIs('developpeur.dashboard') || request()->routeIs('client.dashboard'),
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'badge'  => 0,
                ];

                // Projets — visible par Chef, Testeur, Dev et Client
                if ($isChef || $isTester || $isDev || $isClient) {
                    $dockItems[] = [
                        'id'     => 'dock-projets',
                        'label'  => 'Projets',
                        'route'  => $projetsRoute,
                        'active' => request()->routeIs('projets.*') || request()->routeIs('testeur.projets.*') || request()->routeIs('test-cases.*') || request()->routeIs('testeur.projet.*') || request()->routeIs('testeur.executer'),
                        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
                        'badge'  => 0,
                    ];
                }

                // Équipe — visible par Chef uniquement
                if ($isChef) {
                    $dockItems[] = [
                        'id'     => 'dock-equipe',
                        'label'  => 'Équipe',
                        'route'  => 'equipe.index',
                        'active' => request()->routeIs('equipe.*'),
                        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                        'badge'  => 0,
                    ];

                    // Clients — visible par Chef uniquement
                    $dockItems[] = [
                        'id'     => 'dock-clients',
                        'label'  => 'Clients',
                        'route'  => 'clients.index',
                        'active' => request()->routeIs('clients.*'),
                        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                        'badge'  => 0,
                    ];
                }
            @endphp

            @foreach($dockItems as $item)
                <a href="{{ route($item['route']) }}"
                    id="{{ $item['id'] }}"
                    class="dock-item group relative flex flex-col items-center justify-end h-[60px] transition-all duration-200 cursor-pointer w-16"
                    style="{{ $item['active'] ? 'z-index: 10;' : '' }}">

                    {{-- Badge --}}
                    @if($item['badge'] > 0)
                        <span class="absolute top-0 right-2 w-4 h-4 text-white text-xs rounded-full flex items-center justify-center font-bold z-20 shadow-sm"
                            style="background:#8b0000; font-size:9px;">
                            {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                        </span>
                    @endif

                    {{-- Icône container --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 relative z-10"
                        style="{{ $item['active']
                            ? 'background: #8b0000; box-shadow: 0 4px 15px rgba(139,0,0,0.5); transform: translateY(-4px);'
                            : 'background: rgba(255,255,255,0.05);' }}">
                        <svg class="w-5 h-5 transition-colors duration-200"
                            style="color: {{ $item['active'] ? '#ffffff' : '#9ca3af' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                    </div>

                    {{-- Label (visible uniquement si actif ou au hover) --}}
                    <span class="text-[9px] font-bold uppercase tracking-wider transition-all duration-300 absolute -bottom-4 opacity-0 group-hover:opacity-100 group-hover:-bottom-1"
                        style="color: {{ $item['active'] ? '#ffffff' : '#9ca3af' }}; {{ $item['active'] ? 'opacity: 1; bottom: -1px;' : '' }}">
                        {{ $item['label'] }}
                    </span>
                    
                    {{-- Point indicateur actif (Grille subtile en dessous) --}}
                    @if($item['active'])
                        <span class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-white opacity-50 shadow-[0_0_8px_rgba(255,255,255,0.8)]"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- FAB Bouton + (rouge foncé, bas droite) --}}
        @yield('fab')
    </div>
</div>

{{-- Styles dock & light mode --}}
<style>
    [x-cloak] { display: none !important; }
    
    /* Effets 3D Hover sur les icônes du dock */
    .dock-item:hover > div:first-of-type {
        transform: scale(1.15) translateY(-6px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }
    .dock-item:hover svg {
        color: #ffffff !important;
    }
    .dock-item:hover > div:first-of-type:not([style*="8b0000"]) {
        background: rgba(255,255,255,0.12) !important;
    }
    .dock-item:active > div:first-of-type {
        transform: scale(0.95) translateY(0);
    }
    
    /* Variables et ajustements spécifiques pour le mode clair (inspiré des maquettes) */
    :root {
        --brand-red: #8b0000;
        --brand-red-light: #fbebeb;
    }
    
    body:not(.dark) .bg-white {
        border-color: #f0f0f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    
    /* Titres et accents en rouge foncé */
    body:not(.dark) h1, body:not(.dark) h2, body:not(.dark) h3 {
        color: #1a1a24;
    }
    
</style>

{{-- Scripts --}}
<script>
    // Appliquer le mode sombre AVANT que Alpine ne charge pour éviter le flash
    (function() {
        var dark = localStorage.getItem('darkMode') === 'true';
        if (dark) document.documentElement.classList.add('dark');
    })();

    function darkMode() {
        return {
            dark: localStorage.getItem('darkMode') === 'true',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('darkMode', this.dark);
                if (this.dark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            },
            init() {
                // Synchroniser l'état avec le localStorage
                this.dark = localStorage.getItem('darkMode') === 'true';
            }
        }
    }
</script>

@livewireScripts
@stack('scripts')

@auth
<livewire:messagerie />
@endauth
</body>
</html>
