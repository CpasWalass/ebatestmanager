<!DOCTYPE html>
<html lang="fr" x-data="darkMode()" :class="dark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EbaTestManager')</title>
    <meta name="description" content="@yield('meta_description', 'Plateforme de gestion des tests UAT/IAT - e-Business Afrique')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" style="font-family:'Inter',sans-serif;">

<style>
    [x-cloak] { display: none !important; }

    /* ============ AMBIENT BACKGROUND ============ */
    body {
        background-color: #f1f2f4;
        background-image:
            radial-gradient(ellipse 60% 45% at 15% -8%, rgba(139,0,0,0.06), transparent 60%),
            radial-gradient(ellipse 55% 40% at 100% 100%, rgba(139,0,0,0.05), transparent 60%),
            linear-gradient(rgba(180,185,195,0.35) 1px, transparent 1px),
            linear-gradient(90deg, rgba(180,185,195,0.35) 1px, transparent 1px);
        background-size: auto, auto, 44px 44px, 44px 44px;
    }
    html.dark body {
        background-color: #111827;
        background-image:
            radial-gradient(ellipse 60% 45% at 15% -8%, rgba(139,0,0,0.18), transparent 60%),
            radial-gradient(ellipse 55% 40% at 100% 100%, rgba(139,0,0,0.13), transparent 60%),
            linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px);
        background-size: auto, auto, 44px 44px, 44px 44px;
    }
    body::before { display: none; }

    /* ---- progress ring around the card icon ---- */
    .ring-wrap { position:relative; width:52px; height:52px; flex-shrink:0; }
    .ring-wrap svg { position:absolute; inset:0; transform:rotate(-90deg); }
    .ring-track { stroke:#e5e7eb; }
    html.dark .ring-track { stroke:#374151; }
    .ring-progress { stroke-linecap:round; transition:stroke-dashoffset .6s ease; }

    /* ---- traveling border line, only for "active" cards ---- */
    .live-card { position:relative; }
    .live-card::before {
        content:""; position:absolute; inset:-1px; z-index:0; border-radius:17px;
        background: conic-gradient(from 0deg,
            transparent 0%, transparent 90%,
            rgba(139,0,0,0.15) 94%, rgba(255,90,90,1) 97.5%, rgba(139,0,0,0.15) 100%);
        animation: spin 5s linear infinite;
    }
    .live-card > .card-inner { position:relative; z-index:1; border-radius:16px; background-color: inherit; }
    @keyframes spin{ to{ transform:rotate(360deg); } }
    .live-badge {
        display:inline-flex; align-items:center; gap:5px;
        font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
        color:#8b0000; background:#fef2f2; padding:3px 8px; border-radius:999px;
    }
    html.dark .live-badge { background:rgba(139,0,0,0.18); color:#f87171; }
    .live-dot { width:6px; height:6px; border-radius:50%; background:#dc2626; animation:dotPulse 1.4s ease-in-out infinite; }
    @keyframes dotPulse{ 0%,100%{opacity:.4;} 50%{opacity:1;} }

    @media (prefers-reduced-motion: reduce){
        .live-card::before, .live-dot{ animation:none; }
    }

    /* ============ NAVBAR ============ */
    .app-nav {
        position: fixed; top:0; left:0; right:0; z-index:50;
        background: #ffffff;
        height: 64px;
    }
    html.dark .app-nav { background: #1f2937; }

    .app-nav::after {
        content:""; position:absolute; left:0; right:0; bottom:0; height:3px; z-index:1;
        background: linear-gradient(90deg,
            transparent 0%, transparent 8%,
            rgba(255,120,120,0.95) 16%,
            rgba(139,0,0,1) 24%,
            rgba(255,120,120,0.95) 32%,
            transparent 42%, transparent 100%);
        background-size: 260% 100%;
        animation: veinFlow 6.5s linear infinite, veinPulse 1.9s ease-in-out infinite;
        filter: blur(0.3px);
    }
    @keyframes veinFlow { from { background-position: 100% 0; } to { background-position: -160% 0; } }
    @keyframes veinPulse {
        0%{ opacity:.55; } 8%{ opacity:1; } 18%{ opacity:.6; }
        28%{ opacity:.85; } 45%{ opacity:.55; } 100%{ opacity:.55; }
    }
    @media (prefers-reduced-motion: reduce) { .app-nav::after { animation:none; opacity:.85; } }

    .logo-tilt { perspective:280px; }
    .logo-tilt-inner { transition: transform .3s cubic-bezier(.2,.8,.2,1); transform-style:preserve-3d; }
    .logo-tilt:hover .logo-tilt-inner { transform: rotateY(14deg) rotateX(-8deg); }

    /* ============ FLOATING DOCK ============ */
    #dockRoot { position:fixed; z-index:100; touch-action:none; perspective:900px; }
    #orb {
        position:relative; width:56px; height:56px; border-radius:50%;
        background: #ffffff;
        box-shadow: 0 6px 22px rgba(139,0,0,0.35), 0 2px 6px rgba(0,0,0,0.15), 0 0 0 3px rgba(139,0,0,0.25);
        display:flex; align-items:center; justify-content:center;
        cursor:grab; user-select:none;
        transition: transform .35s cubic-bezier(.34,1.56,.64,1), box-shadow .2s ease;
        transform-style:preserve-3d;
    }
    #orb:active { cursor:grabbing; }
    #orb.dragging { transition:none; }
    #orb.open { transform: rotateY(180deg) scale(1.02); }
    #orb.locked { cursor:pointer; }
    #orb.locked::after {
        content:""; position:absolute; bottom:-2px; right:-2px; width:18px; height:18px;
        border-radius:50%; background:#1f2937; border:2px solid #f9fafb;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><rect x='5' y='11' width='14' height='9' rx='1.5'/><path d='M8 11V8a4 4 0 018 0v3'/></svg>");
        background-size:10px 10px; background-repeat:no-repeat; background-position:center;
    }
    .glyph-front, .glyph-back {
        position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
        backface-visibility:hidden; border-radius:50%;
    }
    .glyph-front img { width:34px; height:34px; object-fit:contain; }
    .glyph-front span { color:#fff; font-weight:800; font-size:13px; }
    .glyph-back { transform: rotateY(180deg); }
    .glyph-back svg { width:18px; height:18px; color:#fff; }

    .fan-item {
        position:absolute; top:50%; left:50%; width:48px; height:48px; margin:-24px 0 0 -24px;
        border-radius:14px; background:#fff; border:1px solid #e5e7eb;
        display:flex; align-items:center; justify-content:center;
        box-shadow:0 6px 18px rgba(0,0,0,0.12);
        opacity:0; pointer-events:none; cursor:pointer;
        transform: translate(0,0) rotate(-140deg) scale(.3);
        transition: transform .5s cubic-bezier(.2,.9,.25,1.4), opacity .3s ease;
        text-decoration:none;
    }
    html.dark .fan-item { background:#1f2937; border-color:#374151; }
    .fan-item svg { width:19px; height:19px; color:#6b7280; transition: color .2s; }
    .fan-item:hover svg { color:#8b0000; }
    .fan-item:hover { box-shadow:0 0 0 2px #8b0000, 0 8px 22px rgba(0,0,0,0.18); }
    .fan-item.fan-active { background:#8b0000; border-color:#8b0000; box-shadow:0 0 0 2px #a10000, 0 6px 18px rgba(139,0,0,0.4); }
    .fan-item.fan-active svg { color:#fff; }

    #dockRoot.open .fan-item {
        opacity:1; pointer-events:auto;
        transform: translate(var(--dx), var(--dy)) rotate(0deg) scale(1);
    }

    .fan-tooltip {
        position:absolute; bottom:calc(100% + 8px); left:50%; transform:translateX(-50%);
        background:#111827; color:#fff; font-size:10px; font-weight:600;
        padding:3px 8px; border-radius:6px; white-space:nowrap;
        opacity:0; pointer-events:none; transition: opacity .15s ease;
    }
    .fan-item:hover .fan-tooltip { opacity:1; }

    .lock-flash {
        position:absolute; inset:-10px; border-radius:50%;
        border:2px solid #8b0000; opacity:0; pointer-events:none;
    }
    .lock-flash.play { animation: lockPulse .5s ease-out; }
    @keyframes lockPulse { 0%{ opacity:1; transform:scale(0.8);} 100%{ opacity:0; transform:scale(1.6);} }

    @media (prefers-reduced-motion: reduce) { #orb,.fan-item { transition-duration:.01ms !important; } }

    /* ============ KPI ring ============ */
    .ring-wrap { position:relative; width:56px; height:56px; flex-shrink:0; }
    .ring-wrap svg { position:absolute; inset:0; transform:rotate(-90deg); }
    .ring-track { stroke:#e5e7eb; }
    html.dark .ring-track { stroke:#374151; }

    /* ============ Avatar ring ============ */
    .avatar-ring { position:relative; width:40px; height:40px; flex-shrink:0; }
    .avatar-ring svg { position:absolute; inset:0; transform:rotate(-90deg); }
    .avatar-track { stroke:#e5e7eb; }
    html.dark .avatar-track { stroke:#374151; }

    /* ============ GENERAL ============ */
    :root { --brand-red: #8b0000; --brand-red-light: #fbebeb; }
    .trend { font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:3px; }
    .badge-overload { color:#b91c1c; background:#fee2e2; }
    html.dark .badge-overload { color:#ff9d9d; background:rgba(139,0,0,0.35); }
    .overload-flag { color:#b91c1c; }
    html.dark .overload-flag { color:#e07a7a; }
</style>

<div class="min-h-screen flex flex-col" style="padding-bottom: 90px;">

    {{-- ═══════════════════════ TOP NAVBAR ═══════════════════════ --}}
    <nav class="app-nav border-b border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-4">

            {{-- Logo & Marque --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="logo-tilt">
                    <div class="logo-tilt-inner flex items-center justify-center h-10 w-10 rounded-lg shadow-sm" style="background:#ffffff; border:1px solid #e5e7eb;">
                        <img src="{{ asset('images/logo-dark.png') }}" alt="Logo" class="h-8 w-8 object-contain"
                             onerror="this.outerHTML='<span class=\'font-bold text-sm\' style=\'color:#8b0000;\'>EB</span>'">
                    </div>
                </div>
                <div class="hidden sm:block leading-none">
                    <p class="text-[10px] font-bold text-gray-800 dark:text-gray-200 uppercase tracking-widest">UAT/IAT Manager</p>
                    <p class="text-base font-bold" style="color:#8b0000;">e-Business Afrique</p>
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

                {{-- Notifications --}}
                @auth
                <livewire:notification-bell />

                {{-- Messagerie --}}
                <button onclick="Livewire.dispatch('openMessagerie')"
                    class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    id="btn-messages" title="Messagerie">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-4-4z"/>
                    </svg>
                </button>

                {{-- Archives --}}
                <button onclick="Livewire.dispatch('openArchives')"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition hidden sm:block"
                    id="btn-apps" title="Rapports archivés">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                @endauth

                {{-- Profil utilisateur --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <div class="w-8 h-8 rounded-lg flex-shrink-0 overflow-hidden">
                            @if(auth()->user()?->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #CC0000, #ff4444);">
                                    {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                                </div>
                            @endif
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white leading-none">{{ auth()->user()?->name ?? 'Utilisateur' }}</p>
                            <p class="text-xs text-gray-400 leading-none mt-0.5">
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
                        class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">

                        <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden">
                                @if(auth()->user()?->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #CC0000, #ff4444);">
                                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()?->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()?->email }}</p>
                            </div>
                        </div>

                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Mon Profil
                        </a>
                        <a href="{{ route('settings.show') }}" class="flex items-center gap-2 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
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

    {{-- ══════════════════════ CONTENU PRINCIPAL ══════════════════════ --}}
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
        {{ $slot ?? '' }}
    </main>

</div>

{{-- ═══════════════════ FLOATING DOCK ═══════════════════ --}}
@auth
@php
    $user    = auth()->user();
    $role    = $user?->getRoleNames()->first() ?? '';
    $isChef  = $role === 'chef_project';
    $isTester= $role === 'tester';
    $isClient= $role === 'client';
    $isDev   = $role === 'developer';

    $dashRoute = match($role) {
        'tester'    => 'testeur.dashboard',
        'developer' => 'developpeur.dashboard',
        'client'    => 'client.dashboard',
        default     => 'dashboard',
    };
    $projetsRoute = match($role) {
        'tester' => 'testeur.projets.index',
        default  => 'projets.index',
    };

    $dockItems = [];

    // Dashboard — tous
    $dockItems[] = [
        'label'  => 'Accueil',
        'route'  => route($dashRoute),
        'active' => request()->routeIs('dashboard') || request()->routeIs('testeur.dashboard') || request()->routeIs('developpeur.dashboard') || request()->routeIs('client.dashboard'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    ];

    // Projets — tous sauf admin pur
    if ($isChef || $isTester || $isDev || $isClient) {
        $dockItems[] = [
            'label'  => 'Projets',
            'route'  => route($projetsRoute),
            'active' => request()->routeIs('projets.*') || request()->routeIs('testeur.projets.*') || request()->routeIs('test-cases.*') || request()->routeIs('testeur.executer'),
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
        ];
    }

    // Équipe, Clients, Journal — chef uniquement
    if ($isChef) {
        $dockItems[] = [
            'label'  => 'Équipe',
            'route'  => route('equipe.index'),
            'active' => request()->routeIs('equipe.*'),
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        ];
        $dockItems[] = [
            'label'  => 'Clients',
            'route'  => route('gestion.clients'),
            'active' => request()->routeIs('gestion.clients'),
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ];
        $dockItems[] = [
            'label'  => 'Journal',
            'route'  => route('journal.global'),
            'active' => request()->routeIs('journal.global'),
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        ];
    }
@endphp

<div id="dockRoot">
    <div id="orb">
        <div class="glyph-front">
            <img src="{{ asset('images/logo-dark.png') }}" alt="EBA"
                 onerror="this.outerHTML='<span>EBA</span>'">
        </div>
        <div class="glyph-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
    </div>
    <div class="lock-flash" id="lockFlash"></div>

    @foreach($dockItems as $dockItem)
    <a href="{{ $dockItem['route'] }}"
       class="fan-item {{ $dockItem['active'] ? 'fan-active' : '' }}"
       title="{{ $dockItem['label'] }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $dockItem['icon'] !!}</svg>
        <span class="fan-tooltip">{{ $dockItem['label'] }}</span>
    </a>
    @endforeach
</div>

<script>
(function(){
    const dockRoot = document.getElementById('dockRoot');
    const orb      = document.getElementById('orb');
    const lockFlash= document.getElementById('lockFlash');
    const fanItems = Array.from(document.querySelectorAll('.fan-item'));
    const RADIUS   = 92;

    let xPct = parseFloat(localStorage.getItem('dockXPct')); if (isNaN(xPct)) xPct = 0.92;
    let yPct = parseFloat(localStorage.getItem('dockYPct')); if (isNaN(yPct)) yPct = 0.9;
    let locked  = localStorage.getItem('dockLocked') === 'true';
    let isOpen  = false, dragging = false, moved = false;
    let startX, startY, startLeft, startTop;

    function applyLockedVisual(){ orb.classList.toggle('locked', locked); }
    applyLockedVisual();

    function layoutDock(){
        const vw = window.innerWidth, vh = window.innerHeight;
        const px = Math.min(Math.max(xPct*vw, 40), vw-40);
        const py = Math.min(Math.max(yPct*vh, 40), vh-40);
        dockRoot.style.left = px + 'px';
        dockRoot.style.top  = py + 'px';
        dockRoot.style.transform = 'translate(-50%,-50%)';
        if(isOpen) positionFan();
    }

    function faceAngleNow(){
        const r = dockRoot.getBoundingClientRect();
        const cy = r.top + r.height/2;
        return cy >= RADIUS + 60 ? -90 : 90;
    }

    function positionFan(){
        const center = faceAngleNow();
        const n = fanItems.length;
        const spread = Math.min(160, n * 35);
        fanItems.forEach((el, i) => {
            const angle = n > 1 ? center - spread/2 + (spread/(n-1))*i : center;
            const rad = angle * Math.PI / 180;
            el.style.setProperty('--dx', (RADIUS * Math.cos(rad)) + 'px');
            el.style.setProperty('--dy', (RADIUS * Math.sin(rad)) + 'px');
            el.style.transitionDelay = isOpen ? (i*35)+'ms' : ((n-1-i)*25)+'ms';
        });
    }

    function openFan(){  isOpen=true;  dockRoot.classList.add('open');  orb.classList.add('open');  positionFan(); }
    function closeFan(){ isOpen=false; dockRoot.classList.remove('open'); orb.classList.remove('open'); positionFan(); }

    orb.addEventListener('click', () => { if(moved){ moved=false; return; } isOpen ? closeFan() : openFan(); });
    document.addEventListener('click', (e) => { if(isOpen && !dockRoot.contains(e.target)) closeFan(); });

    orb.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        locked = !locked;
        localStorage.setItem('dockLocked', locked);
        applyLockedVisual();
        lockFlash.classList.remove('play');
        void lockFlash.offsetWidth;
        lockFlash.classList.add('play');
    });

    orb.addEventListener('mousedown', (e) => {
        if(locked || e.button !== 0) return;
        dragging = true; moved = false;
        const r = dockRoot.getBoundingClientRect();
        startX = e.clientX; startY = e.clientY;
        startLeft = r.left + r.width/2; startTop = r.top + r.height/2;
        orb.classList.add('dragging');
        e.preventDefault();
    });

    window.addEventListener('mousemove', (e) => {
        if(!dragging) return;
        const dx = e.clientX - startX, dy = e.clientY - startY;
        if(!moved && (Math.abs(dx) > 4 || Math.abs(dy) > 4)){ moved = true; if(isOpen) closeFan(); }
        dockRoot.style.left = (startLeft + dx) + 'px';
        dockRoot.style.top  = (startTop  + dy) + 'px';
        dockRoot.style.transform = 'translate(-50%,-50%)';
    });

    window.addEventListener('mouseup', () => {
        if(!dragging) return;
        dragging = false;
        orb.classList.remove('dragging');
        if(moved){
            const r = dockRoot.getBoundingClientRect();
            const vw = window.innerWidth, vh = window.innerHeight;
            xPct = (r.left + r.width/2) / vw;
            yPct = (r.top  + r.height/2) / vh;
            localStorage.setItem('dockXPct', xPct);
            localStorage.setItem('dockYPct', yPct);
        }
        layoutDock();
    });

    window.addEventListener('resize', layoutDock);
    layoutDock();
})();
</script>
@endauth

{{-- Scripts --}}
<script>
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
