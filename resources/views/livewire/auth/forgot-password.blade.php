<div style="height: 100%; display: flex; flex-direction: column;">
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
            <div class="id">TC-022</div><div class="label">Validation formulaire client</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(90deg) translateZ(240px)">
            <div class="id">TC-031</div><div class="label">Export du rapport PDF</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(135deg) translateZ(240px)">
            <div class="id">TC-045</div><div class="label">Synchronisation PWA</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(180deg) translateZ(240px)">
            <div class="id">TC-052</div><div class="label">Accès portail client</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(225deg) translateZ(240px)">
            <div class="id">TC-067</div><div class="label">Planification du sprint</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(270deg) translateZ(240px)">
            <div class="id">TC-078</div><div class="label">Aujourd'hui pluqu'hier et bien mmoins que demain</div>
            <div class="chip"><span>Bienvenue</span></div>
          </div>
          <div class="ticket" style="transform:rotateY(315deg) translateZ(240px)">
            <div class="id">TC-089</div><div class="label">Chaque ticket balaye la plateforme jusqu'à son verdict .</div>
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
      <div class="eyebrow">Assistance sécurisée</div>
      <h1>Mot de passe oublié</h1>
      <p class="sub">Renseignez votre email. Une demande sera envoyée au chef de projet pour la réinitialisation.</p>

      @if ($errorMessage)
          <div class="alert-box alert-error">
              <div class="alert-title">
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  {{ $errorMessage }}
              </div>
          </div>
      @endif

      @if ($status)
          <div class="alert-box alert-status">
              <div class="alert-title">
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  Succès
              </div>
              <p style="margin-top:6px;">{{ $status }}</p>
          </div>
      @endif

      <form wire:submit.prevent="requestReset">
          <div class="field">
              <label for="email">Adresse e-mail</label>
              <input id="email" type="email" wire:model="email"
                     placeholder="votre email de connexion"
                     required autofocus>
              @error('email')
                  <p class="field-error">{{ $message }}</p>
              @enderror
          </div>

          <button class="btn-submit" type="submit" wire:loading.attr="disabled" style="position: relative;">
              <span wire:loading.remove wire:target="requestReset">Envoyer la demande</span>
              <span wire:loading wire:target="requestReset" style="display: none;">Envoi en cours...</span>
          </button>
      </form>

      <div class="row-between">
          <a href="{{ route('login') }}">← Retour à la connexion</a>
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
    if (stageWrap && stage) {
        stageWrap.addEventListener('mousemove', (e) => {
          const r = stageWrap.getBoundingClientRect();
          const x = (e.clientX - r.left) / r.width - 0.5;
          const y = (e.clientY - r.top) / r.height - 0.5;
          stage.style.transform = `rotateX(${y * -8}deg) rotateY(${x * 8}deg)`;
        });
        stageWrap.addEventListener('mouseleave', () => {
          stage.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    }
  </script>
</div>
