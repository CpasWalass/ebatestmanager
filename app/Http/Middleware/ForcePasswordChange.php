<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Routes accessibles même sans avoir changé son mot de passe temporaire.
     * Remplace l'ancien `!str_contains($routeName, 'livewire')`, qui matchait
     * n'importe quelle route contenant "livewire" n'importe où dans son nom
     * (fragile et difficile à auditer). Ici la liste est explicite.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $route = $request->route();
            $routeName = (string) ($route?->getName() ?? '');
            $uri = $request->path();

            if (in_array($routeName, ['profile.show', 'logout'], true)) {
                return $next($request);
            }

            if (str_ends_with($routeName, '-livewire.update')) {
                return $next($request);
            }

            if ($routeName === 'livewire.upload-file' || $routeName === 'livewire.preview-file') {
                return $next($request);
            }

            if (str_contains($uri, 'livewire') && $route?->getPrefix() === 'livewire') {
                return $next($request);
            }

            return redirect()->route('profile.show')
                ->with('warning', 'Vous devez changer votre mot de passe temporaire avant de continuer.');
        }

        return $next($request);
    }
}
