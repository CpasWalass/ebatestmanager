<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Si l'utilisateur est sur / ou /dashboard, rediriger selon son rôle
        if ($request->is('/') || $request->is('dashboard')) {
            if ($user->hasRole('tester')) {
                return redirect()->route('testeur.dashboard');
            }

            if ($user->hasRole('developer')) {
                return redirect()->route('developpeur.dashboard');
            }

            if ($user->hasRole('client')) {
                return redirect()->route('client.dashboard');
            }
        }

        return $next($request);
    }
}
