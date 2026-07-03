<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Contracts\LoginViewResponse::class,
            \App\Http\Responses\LoginViewResponse::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Redirection après login selon le rôle de l'utilisateur
        $this->app->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                public function toResponse($request)
                {
                    $user = auth()->user();
                    if (!$user) return redirect('/login');
                    // must_change_password est géré directement dans store() — on n'arrive ici que si le MDP est normal
                    if ($user->hasRole('tester'))    return redirect()->route('testeur.dashboard');
                    if ($user->hasRole('developer')) return redirect()->route('developpeur.dashboard');
                    if ($user->hasRole('client'))    return redirect()->route('client.dashboard');
                    return redirect()->route('dashboard'); // chef_project par défaut
                }
            };
        });

        $this->app->singleton(\Laravel\Fortify\Contracts\AuthenticateLoginResponse::class, function () {
            return new class {
                public function toResponse($request)
                {
                    return redirect()->intended(config('fortify.home', '/home'));
                }
            };
        });

        $this->app->extend(\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, function ($controller) {
            return new class(app(\Illuminate\Contracts\Auth\StatefulGuard::class)) extends \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController {
                public function store(LoginRequest $request)
                {
                    $credentials = $request->only(Fortify::username(), 'password');
                    $user = \App\Models\User::where(Fortify::username(), $credentials[Fortify::username()])->first();

                    if ($user && $user->locked_until && $user->locked_until->isFuture()) {
                        $secondsLeft = (int) now()->diffInSeconds($user->locked_until, false);
                        $minutesLeft = (int) ceil($secondsLeft / 60);
                        $timeLabel = $minutesLeft > 1
                            ? $minutesLeft . ' minutes'
                            : ($secondsLeft > 1 ? $secondsLeft . ' secondes' : '1 seconde');

                        $message = 'Votre compte est temporairement bloqué. Veuillez réessayer dans ' . $timeLabel . '.';

                        // Stocker les secondes restantes pour le compte à rebours JS
                        session()->flash('lockout_seconds', $secondsLeft);

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            Fortify::username() => $message,
                        ]);
                    }

                    // Si l'utilisateur a un mot de passe temporaire, on le connecte et redirige vers profil
                    if ($user && $user->must_change_password && Hash::check($credentials['password'], $user->password)) {
                        Auth::guard('web')->login($user, $request->filled('remember'));
                        $user->failed_login_attempts = 0;
                        $user->locked_until = null;
                        $user->save();

                        // Ne pas déconnecter ici — laisser l'utilisateur connecté pour changer son MDP
                        return redirect()->route('profile.show')->with('status', 'Veuillez changer votre mot de passe temporaire avant de continuer.');
                    }

                    $authenticated = Auth::guard('web')->attempt($credentials, $request->filled('remember'));

                    if (! $authenticated) {
                        if ($user) {
                            $user->failed_login_attempts = ($user->failed_login_attempts ?? 0) + 1;
                            if ($user->failed_login_attempts >= 5) {
                                $user->locked_until = now()->addMinutes(30);
                                $user->failed_login_attempts = 0;
                            }
                            $user->save();
                        }

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            Fortify::username() => __('Identifiants invalides.'),
                        ]);
                    }

                    $user->failed_login_attempts = 0;
                    $user->locked_until = null;
                    $user->save();

                    return redirect()->intended(config('fortify.home', '/home'));
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
