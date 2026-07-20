<x-mail::message>
# Bonjour {{ $clientName }},

Le Chef de Projet vous invite à participer à la phase de recette (UAT) pour le projet **{{ $projectName }}**.

Un compte sécurisé a été créé pour vous permettre de consulter et valider les cas de test.

**Voici vos identifiants de connexion :**
- **Email :** {{ $email }}
- **Mot de passe :** {{ $password }}

<x-mail::button :url="$url" color="success">
Accéder à mon espace de recette
</x-mail::button>

*Pour des raisons de sécurité, nous vous recommandons de modifier ce mot de passe lors de votre première connexion.*

Si vous avez des questions, n'hésitez pas à répondre directement à ce message.

Merci,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
