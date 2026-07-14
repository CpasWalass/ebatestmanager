<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('darkMode')==='true' }" :class="dark ? 'dark' : ''">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EbaTestManager — Service indisponible</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' };</script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  html,body{ font-family:'Inter',sans-serif; height:100%; }
  [x-cloak]{ display:none !important; }
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
  .app-nav{ position:sticky; top:0; z-index:40; background:#ffffff; }
  html.dark .app-nav{ background:#1f2937; }
  .app-nav::after{
    content:""; position:absolute; left:0; right:0; bottom:0; height:3px; z-index:1;
    background: linear-gradient(90deg, transparent 0%, transparent 8%, rgba(255,120,120,0.95) 16%, rgba(139,0,0,1) 24%, rgba(255,120,120,0.95) 32%, transparent 42%, transparent 100%);
    background-size: 260% 100%;
    animation: veinFlow 6.5s linear infinite, veinPulse 1.9s ease-in-out infinite;
    filter: blur(0.3px);
  }
  @keyframes veinFlow{ from{ background-position:100% 0; } to{ background-position:-160% 0; } }
  @keyframes veinPulse{ 0%{opacity:.55;} 8%{opacity:1;} 18%{opacity:.6;} 28%{opacity:.85;} 45%{opacity:.55;} 100%{opacity:.55;} }
  .logo-tilt{ perspective:280px; }
  .logo-tilt-inner{ transition:transform .3s cubic-bezier(.2,.8,.2,1); transform-style:preserve-3d; }
  .logo-tilt:hover .logo-tilt-inner{ transform:rotateY(14deg) rotateX(-8deg); }
  .error-code {
    font-size: clamp(6rem, 20vw, 10rem); font-weight:800; line-height:1;
    background: linear-gradient(135deg, #8b0000, #c23b3b 50%, #8b0000);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    letter-spacing:-0.04em; filter:drop-shadow(0 4px 24px rgba(139,0,0,0.2));
  }
  .btn-primary{ background:#8b0000; transition:background .2s ease, transform .15s ease; }
  .btn-primary:hover{ background:#a10000; transform:translateY(-1px); }
  .btn-secondary{ transition:background .2s ease, transform .15s ease; }
  .btn-secondary:hover{ transform:translateY(-1px); }
  /* Pulse dot for maintenance */
  .pulse-dot {
    width:12px; height:12px; border-radius:50%; background:#f59e0b;
    animation: pulseDot 2s ease-in-out infinite;
  }
  @keyframes pulseDot {
    0%,100%{ transform:scale(1); box-shadow:0 0 0 0 rgba(245,158,11,0.4); }
    50%{ transform:scale(1.1); box-shadow:0 0 0 8px rgba(245,158,11,0); }
  }
</style>
</head>
<body class="text-gray-900 dark:text-gray-100 flex flex-col">
  <nav class="app-nav relative border-b border-gray-100 dark:border-gray-800" style="height:64px;">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 min-w-0">
        <div class="logo-tilt">
          <div class="logo-tilt-inner flex items-center justify-center h-10 w-10 rounded-lg shadow-sm" style="background:#ffffff;border:1px solid #e5e7eb;">
            <img src="/images/logo-dark.png" alt="Logo" style="height:32px;width:32px;object-fit:contain;" onerror="this.outerHTML='<span style=\'font-weight:800;color:#8b0000;font-size:13px;\'>EB</span>'">
          </div>
        </div>
        <div class="hidden sm:block leading-none">
          <p style="font-size:10px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.12em;">UAT/IAT Manager</p>
          <p style="font-size:15px;font-weight:700;color:#8b0000;">e-Business Afrique</p>
        </div>
      </div>
      <button @click="dark=!dark; localStorage.setItem('darkMode',dark)" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition">
        <svg x-show="!dark" class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"/></svg>
        <svg x-show="dark" class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
      </button>
    </div>
  </nav>
  <main class="flex-1 flex items-center justify-center px-4 py-16 relative z-10">
    <div class="text-center max-w-lg">
      <div class="flex justify-center mb-6">
        <div style="width:80px;height:80px;background:rgba(245,158,11,0.1);border-radius:24px;display:flex;align-items:center;justify-content:center;">
          <svg style="width:40px;height:40px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
      </div>
      <div class="error-code mb-4">503</div>
      <h1 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">Service temporairement indisponible</h1>
      <div class="flex items-center justify-center gap-2 mb-4">
        <div class="pulse-dot"></div>
        <span class="text-sm font-medium text-amber-600 dark:text-amber-400">Maintenance en cours</span>
      </div>
      <p class="text-gray-500 dark:text-gray-400 text-sm mb-10 leading-relaxed">
        L'application est temporairement hors ligne pour maintenance.<br>
        Elle sera de retour très prochainement. Merci de votre patience.
      </p>
      <div class="flex items-center justify-center gap-3 flex-wrap">
        <button onclick="location.reload()" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Actualiser
        </button>
      </div>
    </div>
  </main>
</body>
</html>
