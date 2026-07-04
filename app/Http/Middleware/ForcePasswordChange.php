<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Si l'utilisateur doit changer son mot de passe, on le bloque
     * sur la page profil jusqu'à ce qu'il le fasse.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Routes autorisées même sans avoir changé le mot de passe
            $allowedRoutes = ['profile.show', 'logout'];
            $routeName = (string) $request->route()?->getName();

            if (!in_array($routeName, $allowedRoutes) && !str_contains($routeName, 'livewire')) {
                return redirect()->route('profile.show')
                    ->with('warning', 'Vous devez changer votre mot de passe temporaire avant de continuer.');
            }
        }

        return $next($request);
    }
}
