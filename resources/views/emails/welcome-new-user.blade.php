<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; color: #374151; }
        .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #8b0000, #cc0000); padding: 32px 40px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0 0 8px; }
        .header p { color: rgba(255,255,255,0.8); margin: 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .body p { font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .credentials { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .cred-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 4px; }
        .cred-value { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .cred-value.password { font-size: 22px; font-weight: 700; color: #8b0000; font-family: 'Courier New', monospace; letter-spacing: 0.15em; background: #fff; border: 2px dashed #e2e8f0; border-radius: 8px; padding: 10px 16px; display: inline-block; margin-bottom: 0; }
        .btn-wrap { text-align: center; margin: 24px 0; }
        .btn { display: inline-block; padding: 13px 32px; background: #8b0000; color: white !important; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; }
        .warning { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 12px 16px; margin-top: 16px; font-size: 13px; color: #92400e; line-height: 1.5; }
        .footer { background: #f9fafb; border-top: 1px solid #f0f0f0; padding: 20px 40px; text-align: center; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎉 Bienvenue sur EbaTestManager</h1>
        <p>e-Business Afrique — Plateforme UAT/IAT</p>
    </div>
    <div class="body">
        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
        <p>Votre compte a été créé avec succès sur la plateforme <strong>EbaTestManager</strong>. Voici vos identifiants de connexion :</p>

        <div class="credentials">
            <div class="cred-label">Adresse email</div>
            <div class="cred-value">{{ $user->email }}</div>
            <div class="cred-label">Mot de passe temporaire</div>
            <div class="cred-value password">{{ $temporaryPassword }}</div>
        </div>

        <div class="warning">
            ⚠️ <strong>Important :</strong> Pour votre sécurité, veuillez modifier votre mot de passe dès votre première connexion.
        </div>

        <p style="margin-top: 24px;">Connectez-vous à la plateforme dès maintenant :</p>
        <div class="btn-wrap">
            <a href="{{ config('app.url') }}/login" class="btn">Se connecter →</a>
        </div>

        <p>Si vous avez des questions, contactez votre chef de projet.</p>
        <p>Cordialement,<br><strong>L'équipe EbaTestManager</strong></p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} e-Business Afrique — EbaTestManager. Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</div>
</body>
</html>
