<!DOCTYPE html>
<html lang="fr" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EbaTestManager — @yield('title', 'Erreur')</title>
@vite(['resources/css/app.css'])
<script>
    // Applique le thème sombre avant le premier paint (évite le flash clair/sombre).
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
    }
</script>
<style>
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
    .app-nav { position: sticky; top: 0; z-index: 40; background: #ffffff; }
    html.dark .app-nav { background: #1f2937; }
    .app-nav::after {
        content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 3px; z-index: 1;
        background: linear-gradient(90deg,
            transparent 0%, transparent 8%,
            rgba(255,120,120,0.95) 16%, rgba(139,0,0,1) 24%,
            rgba(255,120,120,0.95) 32%, transparent 42%, transparent 100%);
        background-size: 260% 100%;
        animation: veinFlow 6.5s linear infinite, veinPulse 1.9s ease-in-out infinite;
        filter: blur(0.3px);
    }
    @keyframes veinFlow { from { background-position: 100% 0; } to { background-position: -160% 0; } }
    @keyframes veinPulse { 0% { opacity: .55; } 8% { opacity: 1; } 18% { opacity: .6; } 28% { opacity: .85; } 45% { opacity: .55; } 100% { opacity: .55; } }
    @media (prefers-reduced-motion: reduce) { .app-nav::after { animation: none; opacity: .85; } }

    .error-code {
        font-size: clamp(6rem, 20vw, 10rem);
        font-weight: 800;
        line-height: 1;
        background: linear-gradient(135deg, #8b0000, #c23b3b 50%, #8b0000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.04em;
        filter: drop-shadow(0 4px 24px rgba(139,0,0,0.2));
    }
    .btn-primary { background: #8b0000; transition: background .2s ease, transform .15s ease; }
    .btn-primary:hover { background: #a10000; transform: translateY(-1px); }
    .btn-primary:active { transform: translateY(0); }
    .btn-secondary { transition: background .2s ease, transform .15s ease; }
    .btn-secondary:hover { transform: translateY(-1px); }

    .particle {
        position: absolute; border-radius: 50%; pointer-events: none;
        animation: float linear infinite;
        background: rgba(139,0,0,0.06);
    }
    html.dark .particle { background: rgba(139,0,0,0.15); }
    @keyframes float {
        0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-20px) rotate(720deg); opacity: 0; }
    }
</style>
</head>
<body class="text-gray-900 dark:text-gray-100 flex flex-col">

    <div aria-hidden="true" style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0;">
        <div class="particle" style="left:10%;width:8px;height:8px;animation-duration:18s;animation-delay:0s;"></div>
        <div class="particle" style="left:25%;width:5px;height:5px;animation-duration:22s;animation-delay:3s;"></div>
        <div class="particle" style="left:50%;width:10px;height:10px;animation-duration:16s;animation-delay:6s;"></div>
        <div class="particle" style="left:70%;width:6px;height:6px;animation-duration:20s;animation-delay:2s;"></div>
        <div class="particle" style="left:85%;width:7px;height:7px;animation-duration:24s;animation-delay:9s;"></div>
    </div>

    <nav class="app-nav relative border-b border-gray-100 dark:border-gray-800" style="height:64px;">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg shadow-sm" style="background:#ffffff;border:1px solid #e5e7eb;">
                    <img src="/images/logo-dark.png" alt="Logo" style="height:32px;width:32px;object-fit:contain;"
                         onerror="this.outerHTML='<span style=\'font-weight:800;color:#8b0000;font-size:13px;\'>EB</span>'">
                </div>
                <div class="hidden sm:block leading-none">
                    <p style="font-size:10px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.12em;">UAT/IAT Manager</p>
                    <p style="font-size:15px;font-weight:700;color:#8b0000;">e-Business Afrique</p>
                </div>
            </div>
            <button id="theme-toggle" type="button" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg id="icon-sun" class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"/></svg>
                <svg id="icon-moon" class="w-5 h-5 text-gray-300 hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
            </button>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 py-16 relative z-10">
        <div class="text-center max-w-lg">
            <div class="flex justify-center mb-6">
                <div style="width:80px;height:80px;background:rgba(139,0,0,0.08);border-radius:24px;display:flex;align-items:center;justify-content:center;">
                    @yield('icon')
                </div>
            </div>

            <div class="error-code mb-4">@yield('code')</div>
            <h1 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">@yield('title')</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-10 leading-relaxed">
                @yield('message')
            </p>

            <div class="flex items-center justify-center gap-3 flex-wrap">
                @hasSection('actions')
                    @yield('actions')
                @else
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
                       class="btn-secondary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Retour
                    </a>
                    <a href="/" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Tableau de bord
                    </a>
                @endif
            </div>
        </div>
    </main>

    <script>
        // Toggle thème — vanilla JS, plus besoin d'Alpine (chargé par CDN) pour un
        // simple bouton sur une page d'erreur statique.
        (function () {
            var root = document.documentElement;
            var sun = document.getElementById('icon-sun');
            var moon = document.getElementById('icon-moon');

            function sync() {
                var isDark = root.classList.contains('dark');
                sun.classList.toggle('hidden', isDark);
                moon.classList.toggle('hidden', !isDark);
            }

            document.getElementById('theme-toggle').addEventListener('click', function () {
                root.classList.toggle('dark');
                localStorage.setItem('darkMode', root.classList.contains('dark'));
                sync();
            });

            sync();
        })();
    </script>
</body>
</html>
