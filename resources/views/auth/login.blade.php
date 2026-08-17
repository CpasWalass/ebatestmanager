<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — EbaTestManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/auth.css'])
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
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(45deg) translateZ(240px)">
            <div class="id">TC-022</div><div class="label">l'art et la technologie au service du développement</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(90deg) translateZ(240px)">
            <div class="id">TC-031</div><div class="label">Tout est possible à qui rêve</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(135deg) translateZ(240px)">
            <div class="id">TC-045</div><div class="label">Ose, travaille et n'abandonne jamais</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(180deg) translateZ(240px)">
            <div class="id">TC-052</div><div class="label">Innover c'est savoir abandonner des milliers de bonnes idées</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(225deg) translateZ(240px)">
            <div class="id">TC-067</div><div class="label">Car vois-tu chaque jourje t'aime, </div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(270deg) translateZ(240px)">
            <div class="id">TC-078</div><div class="label">Aujourd'hui pluqu'hier et bien mmoins que demain</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(315deg) translateZ(240px)">
            <div class="id">TC-089</div><div class="label">Les vraies passions donnent des forces en donnant du courage</div>
            <div class="chip"><span>Bienvenue</span></div>
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

      <form method="POST" action="{{ route('login') }}">
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
              <div class="demo-account" onclick="fillLogin('client@ebatest.local')">
                  <span class="role-badge">Client</span>
                  <span class="demo-email">client@ebatest.local</span>
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