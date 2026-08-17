<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Fortify;

/**
 * IMPORTANT — bug corrigé ici :
 *
 * La version précédente remplaçait entièrement AuthenticatedSessionController::store()
 * pour gérer le verrouillage de compte et le mot de passe temporaire. Ce faisant, elle
 * appelait directement Auth::guard('web')->attempt($credentials) et court-circuitait
 * TOUT le pipeline d'authentification de Fortify — y compris la vérification de la
 * double authentification (2FA). Un utilisateur ayant activé la 2FA se retrouvait donc
 * connecté sans jamais passer par l'écran de code, alors que la 2FA est bien activée
 * dans config/fortify.php (Features::twoFactorAuthentication()).
 *
 * Ici, on utilise Fortify::authenticateUsing(), le point d'extension officiel prévu
 * pour personnaliser la vérification des identifiants SANS remplacer le contrôleur.
 * Fortify garde la main après ce callback et continue son pipeline normal (2FA,
 * connexion, redirection) — la logique de verrouillage/mot de passe temporaire est
 * juste un filtre en amont.
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LoginViewResponse::class,
            \App\Http\Responses\LoginViewResponse::class,
        );
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where(Fortify::username(), $request->input(Fortify::username()))->first();

            if (! $user) {
                return null;
            }

            if ($user->locked_until && $user->locked_until->isFuture()) {
                $secondsLeft = (int) now()->diffInSeconds($user->locked_until, false);
                $minutesLeft = (int) ceil($secondsLeft / 60);
                $timeLabel = $minutesLeft > 1
                    ? $minutesLeft.' minutes'
                    : ($secondsLeft > 1 ? $secondsLeft.' secondes' : '1 seconde');

                session()->flash('lockout_seconds', $secondsLeft);

                throw ValidationException::withMessages([
                    Fortify::username() => "Votre compte est temporairement bloqué. Veuillez réessayer dans {$timeLabel}.",
                ]);
            }

            if (! $user->is_active) {
                throw ValidationException::withMessages([
                    Fortify::username() => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                ]);
            }

            // Mot de passe temporaire : on authentifie normalement (donc la 2FA
            // s'applique aussi dans ce cas) — c'est le middleware ForcePasswordChange
            // qui redirige ensuite vers la page profil pour forcer le changement.
            if ($user->must_change_password && $user->temporary_password_hash && Hash::check($request->input('password'), $user->temporary_password_hash)) {
                $user->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();

                return $user;
            }

            if (Hash::check($request->input('password'), $user->password)) {
                $user->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();

                return $user;
            }

            $user->failed_login_attempts = ($user->failed_login_attempts ?? 0) + 1;
            if ($user->failed_login_attempts >= 5) {
                $user->locked_until = now()->addMinutes(30);
                $user->failed_login_attempts = 0;
            }
            $user->save();

            return null;
        });

        // Redirection après connexion selon le rôle. Ce hook (LoginResponse) est
        // bien appelé APRÈS que Fortify ait géré la 2FA si besoin — contrairement
        // à l'ancienne version, on ne le court-circuite plus.
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    if (session()->has('url.intended')) {
                        return redirect()->intended();
                    }

                    $user = auth()->user();

                    if ($user?->must_change_password) {
                        return redirect()->route('profile.show')
                            ->with('status', 'Veuillez changer votre mot de passe temporaire avant de continuer.');
                    }

                    return match (true) {
                        $user?->hasRole('tester') => redirect()->route('testeur.dashboard'),
                        $user?->hasRole('developer') => redirect()->route('developpeur.dashboard'),
                        $user?->hasRole('client') => redirect()->route('client.dashboard'),
                        default => redirect()->route('dashboard'),
                    };
                }
            };
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
