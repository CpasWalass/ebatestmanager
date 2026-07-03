<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — EBA Test Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f8f9fc;
        }

        /* ── Panneau gauche (branding) ── */
        .brand-panel {
            width: 45%;
            background: #8b0000;
            background-image: linear-gradient(135deg, #8b0000 0%, #5a0000 60%, #3b0000 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -100px; right: -100px;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            bottom: -80px; left: -60px;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 60px;
            position: relative; z-index: 1;
        }
        .brand-logo img {
            width: auto; height: 52px;
            max-width: 100%;
            object-fit: contain;
        }
        .brand-logo-fallback {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800;
            color: #8b0000;
            flex-shrink: 0;
        }
        .brand-logo-text {
            display: flex; flex-direction: column;
        }
        .brand-logo-text span:first-child {
            font-size: 10px; font-weight: 700;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase; letter-spacing: 3px;
        }
        .brand-logo-text span:last-child {
            font-size: 20px; font-weight: 700;
            color: white; line-height: 1.2;
        }
        .brand-headline {
            position: relative; z-index: 1;
        }
        .brand-headline h1 {
            font-size: 36px; font-weight: 800;
            color: white; line-height: 1.2;
            margin-bottom: 16px;
        }
        .brand-headline p {
            font-size: 15px; color: rgba(255,255,255,0.65);
            line-height: 1.6; max-width: 340px;
        }
        .brand-stats {
            display: flex; gap: 32px;
            margin-top: 52px;
            position: relative; z-index: 1;
        }
        .brand-stat {
            display: flex; flex-direction: column;
            align-items: flex-start;
        }
        .brand-stat strong {
            font-size: 26px; font-weight: 800;
            color: white;
        }
        .brand-stat span {
            font-size: 12px; color: rgba(255,255,255,0.55);
            margin-top: 2px;
        }
        .brand-divider {
            width: 1px; background: rgba(255,255,255,0.15);
            height: 40px; align-self: center;
        }

        /* ── Panneau droit (formulaire) ── */
        .login-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-box {
            width: 100%; max-width: 420px;
        }
        .login-box h2 {
            font-size: 26px; font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .login-box .subtitle {
            font-size: 14px; color: #6b7280;
            margin-bottom: 36px;
        }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 12px; font-weight: 600;
            color: #374151; text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px; color: #111827;
            background: #fafafa;
            outline: none;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .form-group input:focus {
            border-color: #8b0000;
            background: white;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.08);
        }
        .form-group .error { font-size: 12px; color: #dc2626; margin-top: 5px; }

        .error-alert {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 16px;
            font-size: 13px; color: #dc2626;
            margin-bottom: 20px;
        }
        .lockout-alert {
            background: #fff7ed; border-color: #fed7aa;
            color: #c2410c;
        }
        .lockout-alert #lockout-timer {
            font-size: 15px;
            font-variant-numeric: tabular-nums;
            letter-spacing: 1px;
        }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px;
        }
        .remember-row input[type=checkbox] {
            width: 16px; height: 16px;
            accent-color: #8b0000;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 13px; color: #6b7280;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #8b0000;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px; font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover { background: #6b0000; }
        .btn-login:active { transform: scale(0.99); }

        /* Demo accounts */
        .demo-section {
            margin-top: 32px;
            border-top: 1px solid #f3f4f6;
            padding-top: 24px;
        }
        .demo-section h3 {
            font-size: 11px; font-weight: 700;
            color: #9ca3af; text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
        }
        .demo-accounts {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .demo-account {
            padding: 10px 12px;
            border: 1.5px solid #f3f4f6;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
            display: flex; flex-direction: column; gap: 2px;
            background: white;
        }
        .demo-account:hover {
            border-color: #8b0000;
            background: #fff8f8;
        }
        .demo-account .role-badge {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: #8b0000;
        }
        .demo-account .demo-email {
            font-size: 12px; font-weight: 500;
            color: #374151; font-family: monospace;
        }

        @media (max-width: 768px) {
            .brand-panel { display: none; }
        }
    </style>
</head>
<body>

    <!-- Panneau Branding -->
    <div class="brand-panel">
        <div class="brand-logo">
            <img src="{{ asset('images/logo-dark.png') }}" 
                 alt="Logo" 
                 onerror="this.outerHTML='<div class=\'brand-logo-fallback\'>EB</div>'">
            <div class="brand-logo-text">
                <span>UAT/IAT Manager</span>
                <span>e-Business Afrique</span>
            </div>
        </div>

        <div class="brand-headline">
            <h1>Gérez vos tests logiciels en toute sérénité</h1>
            <p>Plateforme centralisée pour le suivi UAT/IAT, l'exécution des cas de test et la génération de rapports professionnels.</p>
        </div>

        <div class="brand-stats">
            <div class="brand-stat">
                <strong>4</strong>
                <span>Rôles utilisateurs</span>
            </div>
            <div class="brand-divider"></div>
            <div class="brand-stat">
                <strong>UAT</strong>
                <span>Recette Client</span>
            </div>
            <div class="brand-divider"></div>
            <div class="brand-stat">
                <strong>IAT</strong>
                <span>Tests Internes</span>
            </div>
        </div>
    </div>

    <!-- Panneau Connexion -->
    <div class="login-panel">
        <div class="login-box">
            <h2>Connexion</h2>
            <p class="subtitle">Accédez à votre espace de gestion de tests</p>

            @if (session()->has('lockout_seconds'))
                <div class="error-alert lockout-alert" id="lockout-block">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <strong>Compte temporairement bloqué</strong>
                    </div>
                    <p style="margin:0;font-size:13px;">Trop de tentatives incorrectes. Réessayez dans <strong id="lockout-timer">--:--</strong>.</p>
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
                <div class="error-alert">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ $errors->first() }}
                    </div>
                </div>
            @elseif (session('status'))
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:12px 16px;font-size:13px;color:#15803d;margin-bottom:20px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           placeholder="votre@email.com"
                           autocomplete="email"
                           required autofocus>
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               style="padding-right:44px;"
                               required>
                        <button type="button" id="toggle-pwd"
                            onclick="togglePassword()"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#9ca3af;display:flex;align-items:center;"
                            title="Afficher / masquer le mot de passe">
                            <svg id="eye-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-off-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>
            </form>

            <!-- Comptes démo -->
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
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOn = document.getElementById('eye-icon');
            const eyeOff = document.getElementById('eye-off-icon');
            const btn = document.getElementById('toggle-pwd');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOn.style.display = 'none';
                eyeOff.style.display = 'block';
                btn.style.color = '#8b0000';
            } else {
                input.type = 'password';
                eyeOn.style.display = 'block';
                eyeOff.style.display = 'none';
                btn.style.color = '#9ca3af';
            }
        }

        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
        }
    </script>
</body>
</html>
