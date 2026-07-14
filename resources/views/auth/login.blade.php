<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — EbaTestManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f9fafb;
    --panel:#ffffff;
    --panel-line:#e5e7eb;
    --text:#111827;
    --muted:#6b7280;
    --card:#ffffff;
    --card-line:#e5e7eb;
    --card-shadow: 0 1px 3px rgba(0,0,0,0.06);
    --input-bg:#ffffff;
    --input-line:#d1d5db;
    --scene-bg: radial-gradient(ellipse 65% 55% at 50% 45%, rgba(139,0,0,0.14), transparent 72%), #f3f4f6;
    --grid-line: rgba(0,0,0,0.035);
    --ticket-line: rgba(139,0,0,0.16);

    --red:#8b0000;
    --red-light:#b23a3a;
    --red-ring:#CC0000;
    --green:#16a34a;
  }
  html.dark{
    --bg:#111827;
    --panel:#1f2937;
    --panel-line:#374151;
    --text:#f9fafb;
    --muted:#9ca3af;
    --card:#1f2937;
    --card-line:#374151;
    --card-shadow: 0 1px 3px rgba(0,0,0,0.3);
    --input-bg:#111827;
    --input-line:#374151;
    --scene-bg: radial-gradient(ellipse 65% 55% at 50% 45%, rgba(139,0,0,0.30), transparent 72%), #0f172a;
    --grid-line: rgba(255,255,255,0.035);
    --ticket-line: rgba(196,60,60,0.28);
    --red-light:#d16565;
    --green:#22c55e;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html,body{height:100%;}
  body{
    background:var(--bg);
    color:var(--text);
    font-family:'Inter', sans-serif;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    transition: background .25s ease, color .25s ease;
  }
  .brand-bar{
    height:4px; width:100%;
    background:linear-gradient(90deg, var(--red), #b23a3a, var(--red));
    flex-shrink:0;
  }
  .split{ flex:1; display:flex; min-height:0; }
  @media (prefers-reduced-motion: reduce){
    .orbit, .beam{ animation: none !important; }
  }

  .theme-toggle{
    position:absolute; top:40px; right:40px; z-index:10;
    width:38px; height:38px; border-radius:10px;
    border:1px solid var(--panel-line);
    background:var(--panel);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer;
  }
  .theme-toggle svg{ width:18px; height:18px; }
  .theme-toggle .sun{ color:#f59e0b; display:none; }
  .theme-toggle .moon{ color:#9ca3af; display:block; }
  html.dark .theme-toggle .sun{ display:block; }
  html.dark .theme-toggle .moon{ display:none; }

  .scene-side{
    position:relative;
    flex:1.15;
    display:flex; align-items:center; justify-content:center;
    background:var(--scene-bg);
    overflow:hidden;
    border-right:1px solid var(--panel-line);
  }
  .scene-side::before{
    content:"";
    position:absolute; inset:0;
    background-image:
      linear-gradient(var(--grid-line) 1px, transparent 1px),
      linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
    background-size: 46px 46px;
    mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 75%);
  }

  .brand-mark{
    position:absolute; top:44px; left:48px;
    display:flex; align-items:center; gap:10px; z-index:5;
  }
  .brand-mark .glyph{
    width:60px; height:60px; border-radius:9px;
    /*background:#ffffff;*/
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:12px; color:var(--red);
    flex-shrink:0; overflow:hidden;
  }
  .brand-mark .glyph img{
    width:170%; height:170%; object-fit:contain; padding:6px;
  }
  .brand-mark .name{ font-size:10px; letter-spacing:0.14em; text-transform:uppercase; color:var(--muted); font-weight:600;}
  .brand-mark .name b{ color:var(--text); display:block; font-size:14px; letter-spacing:0; text-transform:none; font-weight:700; margin-top:2px;}

  .stage-wrap{ perspective:1400px; width:600px; height:600px; display:flex; align-items:center; justify-content:center; }
  .stage{
    position:relative; width:1px; height:1px;
    transform-style:preserve-3d;
    transition: transform .25s ease-out;
  }

  .orbit{
    position:absolute; top:0; left:0; width:1px; height:1px;
    transform-style:preserve-3d;
    animation: spin 26s linear infinite;
  }
  @keyframes spin{ from{ transform:rotateY(0deg);} to{ transform:rotateY(360deg);} }

  .hub{
    position:absolute; top:50%; left:50%;
    width:84px; height:84px; margin:-42px 0 0 -42px;
    border-radius:20px;
    background:linear-gradient(155deg, #a10000, var(--red) 60%, #6b0000);
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 0 46px rgba(139,0,0,0.45), 0 6px 20px rgba(0,0,0,0.25);
    z-index:3;
  }
  .hub span{ font-weight:800; color:#fff; font-size:20px; }
  .hub-badge{
    width:56px; height:56px; border-radius:14px;
    background:#ffffff;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
  }
  .hub-badge img{ width:100%; height:100%; object-fit:contain; padding:8px; }

  .ticket{
    position:absolute; top:50%; left:50%;
    width:172px; height:100px; margin:-50px 0 0 -86px;
    background:var(--card);
    border:1px solid var(--ticket-line);
    border-radius:10px;
    padding:14px 16px;
    box-shadow: var(--card-shadow);
    backface-visibility:hidden;
    transition: border-color .5s ease, box-shadow .5s ease;
  }
  .ticket .id{ font-size:11px; color:var(--muted); font-weight:600; letter-spacing:0.03em; }
  .ticket .label{ font-size:12.5px; font-weight:500; margin-top:7px; line-height:1.3; color:var(--text); }
  .ticket .chip{
    margin-top:12px; display:inline-flex; align-items:center; gap:5px;
    font-size:10px; font-weight:700; letter-spacing:0.03em; text-transform:uppercase;
    color:var(--muted);
  }
  .ticket .chip::before{ content:""; width:6px; height:6px; border-radius:50%; background:var(--muted); }

  .ticket.is-active{
    border-color: rgba(139,0,0,0.45);
    box-shadow: var(--card-shadow), 0 0 26px rgba(139,0,0,0.18);
  }
  .ticket.validated .chip{ color:var(--green); }
  .ticket.validated .chip::before{ background:var(--green); box-shadow:0 0 8px rgba(34,197,94,0.7); }
  .ticket.validated .chip span::after{ content:" ✓"; }

  .beam{
    position:absolute; top:6%; bottom:6%;
    width:150px; left:50%; margin-left:-75px;
    background:linear-gradient(90deg, transparent, rgba(139,0,0,0.14) 45%, rgba(139,0,0,0.05) 50%, rgba(139,0,0,0.14) 55%, transparent);
    filter:blur(2px);
    animation: sweep 6s ease-in-out infinite;
    pointer-events:none; z-index:2;
  }
  @keyframes sweep{
    0%,100%{ transform:translateX(-230px); opacity:0; }
    10%{ opacity:1; }
    50%{ transform:translateX(230px); opacity:1; }
    90%{ opacity:1; }
  }

  .caption{ position:absolute; bottom:52px; left:0; right:0; text-align:center; z-index:5; padding:0 40px; }
  .caption h2{ font-weight:700; font-size:20px; color:var(--text); }
  .caption p{ margin-top:8px; font-size:13px; color:var(--muted); max-width:360px; margin-left:auto; margin-right:auto; line-height:1.5; }

  .form-side{ flex:0.85; display:flex; align-items:center; justify-content:center; background:var(--panel); position:relative; overflow-y:auto; }
  .form-wrap{ width:100%; max-width:360px; padding:32px 40px; }

  .form-wrap .eyebrow{ font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--red); margin-bottom:10px; }
  .form-wrap h1{ font-weight:800; font-size:27px; line-height:1.15; margin-bottom:8px; color:var(--text); position:relative; padding-bottom:14px; }
  .form-wrap h1::after{ content:""; position:absolute; left:0; bottom:0; width:36px; height:3px; border-radius:2px; background:var(--red); }
  .form-wrap .sub{ font-size:13.5px; color:var(--muted); margin-bottom:28px; line-height:1.5; }

  /* ── Alertes (erreurs / verrouillage / statut) ── */
  .alert-box{ margin-bottom:20px; border-radius:10px; padding:12px 16px; font-size:13px; line-height:1.5; }
  .alert-box .alert-title{ display:flex; align-items:center; gap:8px; font-weight:600; }
  .alert-box svg{ flex-shrink:0; }
  .alert-error{ background: rgba(220,38,38,0.08); border:1px solid rgba(220,38,38,0.25); color:#dc2626; }
  html.dark .alert-error{ background: rgba(220,38,38,0.12); border-color: rgba(220,38,38,0.35); color:#f87171; }
  .alert-lockout{ background: rgba(194,65,12,0.08); border:1px solid rgba(194,65,12,0.25); color:#c2410c; }
  html.dark .alert-lockout{ background: rgba(194,65,12,0.12); border-color: rgba(194,65,12,0.35); color:#fb923c; }
  .alert-status{ background: rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.25); color:#15803d; }
  html.dark .alert-status{ background: rgba(22,163,74,0.12); border-color: rgba(22,163,74,0.35); color:#4ade80; }
  #lockout-timer{ font-variant-numeric: tabular-nums; letter-spacing:1px; }

  .field{ margin-bottom:18px; }
  .field label{ display:block; font-size:12px; font-weight:600; color:var(--text); margin-bottom:7px; }
  .field input{
    width:100%; padding:11px 14px; border-radius:9px;
    background:var(--input-bg); border:1px solid var(--input-line);
    color:var(--text); font-size:14px; font-family:'Inter',sans-serif;
    outline:none; transition: border-color .2s ease, box-shadow .2s ease;
  }
  .field input::placeholder{ color:var(--muted); }
  .field input:focus{ border-color: var(--red-ring); box-shadow: 0 0 0 3px rgba(204,0,0,0.15); }
  .field-error{ font-size:12px; color:#dc2626; margin-top:6px; }
  html.dark .field-error{ color:#f87171; }

  .pwd-wrap{ position:relative; }
  .pwd-wrap input{ padding-right:42px; }
  .pwd-toggle{ position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; padding:0; color:var(--muted); display:flex; align-items:center; }
  .pwd-toggle:hover{ color:var(--red); }
  .pwd-toggle svg{ width:18px; height:18px; }

  .row-between{ display:flex; align-items:center; justify-content:space-between; margin-bottom:26px; font-size:12.5px; }
  .remember{ display:flex; align-items:center; gap:7px; color:var(--muted); }
  .remember input{ accent-color:var(--red); width:14px; height:14px; }
  .row-between a{ color:var(--red); text-decoration:none; font-weight:500; }
  .row-between a:hover{ text-decoration:underline; }

  .btn-submit{
    width:100%; padding:12px; border:none; border-radius:9px; cursor:pointer;
    background:linear-gradient(135deg, #a10000, var(--red) 70%);
    color:#fff; font-weight:600; font-size:14.5px; font-family:'Inter',sans-serif;
    box-shadow: 0 8px 22px rgba(139,0,0,0.32);
    transition: box-shadow .15s ease, transform .15s ease;
  }
  .btn-submit:hover{ box-shadow: 0 10px 26px rgba(139,0,0,0.45); transform:translateY(-1px); }
  .btn-submit:active{ transform:translateY(0); }

  .foot-note{ margin-top:26px; text-align:center; font-size:12px; color:var(--muted); }

  /* ── Comptes de démonstration ── */
  .demo-section{ margin-top:28px; padding-top:22px; border-top:1px solid var(--panel-line); }
  .demo-section h3{ font-size:10.5px; font-weight:700; letter-spacing:0.09em; text-transform:uppercase; color:var(--muted); margin-bottom:12px; }
  .demo-accounts{ display:grid; grid-template-columns:1fr 1fr; gap:8px; }
  .demo-account{ padding:9px 11px; border:1px solid var(--panel-line); border-radius:9px; cursor:pointer; transition:all .15s ease; display:flex; flex-direction:column; gap:2px; background:var(--card); }
  .demo-account:hover{ border-color:var(--red); background: rgba(139,0,0,0.06); }
  .demo-account .role-badge{ font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--red); }
  .demo-account .demo-email{ font-size:11.5px; font-weight:500; color:var(--text); font-family:monospace; }

  @media (max-width: 880px){
    body{ flex-direction:column; overflow:auto; height:auto; min-height:100vh; }
    .scene-side{ min-height:40vh; border-right:none; border-bottom:1px solid var(--panel-line); }
    .stage-wrap{ width:340px; height:340px; transform:scale(0.8); }
    .form-side{ flex:1; padding:32px 0; }
    .theme-toggle{ top:20px; right:20px; }
    .caption{ bottom:28px; }
    .caption h2{ font-size:17px; }
  }

  @media (max-width: 560px){
    .scene-side{ min-height:32vh; }
    .stage-wrap{ width:260px; height:260px; transform:scale(0.72); }
    .brand-mark{ top:20px; left:20px; }
    .brand-mark .name{ font-size:9px; }
    .brand-mark .name b{ font-size:12.5px; }
    .caption{ display:none; }
    .form-wrap{ padding:24px 24px; max-width:100%; }
    .form-wrap h1{ font-size:23px; }
    .demo-accounts{ grid-template-columns:1fr; }
    .row-between{ flex-wrap:wrap; gap:10px; }
  }

  @media (max-width: 360px){
    .theme-toggle{ width:34px; height:34px; top:14px; right:14px; }
    .brand-mark .glyph{ width:34px; height:34px; }
  }
</style>
</head>
<body>

  <div class="brand-bar"></div>
  <div class="split">
  <button class="theme-toggle" onclick="toggleTheme()" aria-label="Basculer le thème">
    <svg class="sun" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.464 5.464a1 1 0 01.707-.293h.026a1 1 0 010 2 1 1 0 01-.733-1.707zM5 11a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"/></svg>
    <svg class="moon" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
  </button>

  <section class="scene-side">
    <div class="brand-mark">
      <div class="glyph">
        <img src="{{ asset('images/logo-dark.png') }}" alt="Logo e-Business Afrique"
             onerror="this.replaceWith(Object.assign(document.createElement('span'),{textContent:'EB'}))">
      </div>
      <div class="name">e-Business Afrique<b>UAT/IAT Manager</b></div>
    </div>

    <div class="stage-wrap" id="stageWrap">
      <div class="stage" id="stage">
        <div class="beam"></div>
        <div class="orbit" id="orbit">
          <div class="hub">
            <div class="hub-badge">
              <img src="{{ asset('images/logo-dark.png') }}" alt="Logo e-Business Afrique"
                   onerror="this.parentElement.replaceWith(Object.assign(document.createElement('span'),{textContent:'QA'}))">
            </div>
          </div>

          <div class="ticket" style="transform:rotateY(0deg) translateZ(240px)">
            <div class="id">TC-014</div><div class="label">Connexion utilisateur</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(45deg) translateZ(240px)">
            <div class="id">TC-022</div><div class="label">Validation formulaire client</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(90deg) translateZ(240px)">
            <div class="id">TC-031</div><div class="label">Export du rapport PDF</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(135deg) translateZ(240px)">
            <div class="id">TC-045</div><div class="label">Synchronisation PWA</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(180deg) translateZ(240px)">
            <div class="id">TC-052</div><div class="label">Accès portail client</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(225deg) translateZ(240px)">
            <div class="id">TC-067</div><div class="label">Planification du sprint</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(270deg) translateZ(240px)">
            <div class="id">TC-078</div><div class="label">Exécution du cas de test</div>
            <div class="chip"><span>En attente</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(315deg) translateZ(240px)">
            <div class="id">TC-089</div><div class="label">Notifications temps réel</div>
            <div class="chip"><span>En attente</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="caption">
      <h2>Vos cas de test, en orbite vers la validation.</h2>
      <p>Chaque ticket balaye la plateforme jusqu'à son verdict . Suivez vos campagnes UAT/IAT en un coup d'œil.</p>
    </div>
  </section>

  <section class="form-side">
    <div class="form-wrap">
      <div class="eyebrow">Espace sécurisé</div>
      <h1>Connexion</h1>
      <p class="sub">Accédez à vos projets, sprints et campagnes de test.</p>

      @if (session()->has('lockout_seconds'))
          <div class="alert-box alert-lockout" id="lockout-block">
              <div class="alert-title">
                  <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                  Compte temporairement bloqué
              </div>
              <p style="margin-top:6px;">Trop de tentatives incorrectes. Réessayez dans <strong id="lockout-timer">--:--</strong>.</p>
          </div>
          <script>
              (function() {
                  let seconds = {{ session('lockout_seconds') }};
                  const el = document.getElementById('lockout-timer');
                  function update() {
                      if (seconds <= 0) {
                          document.getElementById('lockout-block').innerHTML = '<p style="margin:0;">Vous pouvez réessayer de vous connecter.</p>';
                          return;
                      }
                      const m = Math.floor(seconds / 60);
                      const s = seconds % 60;
                      el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                      seconds--;
                      setTimeout(update, 1000);
                  }
                  update();
              })();
          </script>
      @elseif ($errors->any())
          <div class="alert-box alert-error">
              <div class="alert-title">
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  {{ $errors->first() }}
              </div>
          </div>
      @elseif (session('status'))
          <div class="alert-box alert-status">
              {{ session('status') }}
          </div>
      @endif

      <form method="POST" action="{{ route('login.store') }}">
          @csrf

          <div class="field">
              <label for="email">Adresse e-mail</label>
              <input id="email" type="email" name="email"
                     value="{{ old('email') }}"
                     placeholder="votre email de connexion"
                     autocomplete="username" required autofocus>
              @error('email')
                  <p class="field-error">{{ $message }}</p>
              @enderror
          </div>

          <div class="field">
              <label for="password">Mot de passe</label>
              <div class="pwd-wrap">
                  <input id="password" type="password" name="password"
                         placeholder="••••••••"
                         autocomplete="current-password" required>
                  <button type="button" class="pwd-toggle" onclick="togglePassword()" title="Afficher / masquer le mot de passe">
                      <svg id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <svg id="eye-off-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                      </svg>
                  </button>
              </div>
              @error('password')
                  <p class="field-error">{{ $message }}</p>
              @enderror
          </div>

          <div class="row-between">
              <label class="remember"><input type="checkbox" name="remember" id="remember"> Se souvenir de moi</label>
              <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
          </div>

          <button class="btn-submit" type="submit">Se connecter</button>
      </form>

      <div class="demo-section">
          <h3>Comptes de démonstration (mdp : password)</h3>
          <div class="demo-accounts">
              <div class="demo-account" onclick="fillLogin('chef@ebatest.local')">
                  <span class="role-badge">Chef de Projet</span>
                  <span class="demo-email">chef@ebatest.local</span>
              </div>
              <div class="demo-account" onclick="fillLogin('testeur@ebatest.local')">
                  <span class="role-badge">Testeur</span>
                  <span class="demo-email">testeur@ebatest.local</span>
              </div>
              <div class="demo-account" onclick="fillLogin('dev@ebatest.local')">
                  <span class="role-badge">Développeur</span>
                  <span class="demo-email">dev@ebatest.local</span>
              </div>
              <div class="demo-account" onclick="fillLogin('client@bubedra.bj')">
                  <span class="role-badge">Client (BUBEDRA)</span>
                  <span class="demo-email">client@bubedra.bj</span>
              </div>
          </div>
      </div>

      <p class="foot-note">EbaTestManager © 2026 ° e-Business Afrique</p>
    </div>
  </section>
  </div>

<script>
  (function(){
    var dark = localStorage.getItem('darkMode') === 'true';
    if(dark) document.documentElement.classList.add('dark');
  })();
  function toggleTheme(){
    var isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', isDark);
  }

  function togglePassword() {
      const input = document.getElementById('password');
      const eyeOn = document.getElementById('eye-icon');
      const eyeOff = document.getElementById('eye-off-icon');
      const btn = document.querySelector('.pwd-toggle');
      if (input.type === 'password') {
          input.type = 'text';
          eyeOn.style.display = 'none';
          eyeOff.style.display = 'block';
          btn.style.color = 'var(--red)';
      } else {
          input.type = 'password';
          eyeOn.style.display = 'block';
          eyeOff.style.display = 'none';
          btn.style.color = 'var(--muted)';
      }
  }

  function fillLogin(email) {
      document.getElementById('email').value = email;
      document.getElementById('password').value = 'password';
      document.getElementById('email').focus();
  }

  const tickets = document.querySelectorAll('.ticket');
  let i = 0;
  setInterval(() => {
    tickets.forEach(t => t.classList.remove('is-active'));
    tickets[i].classList.add('is-active');
    setTimeout(() => tickets[i].classList.add('validated'), 700);
    i = (i + 1) % tickets.length;
    if (i === 0) setTimeout(() => tickets.forEach(t => t.classList.remove('validated')), 3200);
  }, 2200);

  const stageWrap = document.getElementById('stageWrap');
  const stage = document.getElementById('stage');
  stageWrap.addEventListener('mousemove', (e) => {
    const r = stageWrap.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width - 0.5;
    const y = (e.clientY - r.top) / r.height - 0.5;
    stage.style.transform = `rotateX(${y * -8}deg) rotateY(${x * 8}deg)`;
  });
  stageWrap.addEventListener('mouseleave', () => {
    stage.style.transform = 'rotateX(0deg) rotateY(0deg)';
  });
</script>

</body>
</html>