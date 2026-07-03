<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class BlockLockedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input(Fortify::username());

        if ($email) {
            $user = \App\Models\User::where(Fortify::username(), $email)->first();
            if ($user && $user->locked_until && $user->locked_until->isFuture()) {
                $message = __('Votre compte est temporairement bloqué. Veuillez réessayer dans 30 minutes.');

                session()->flash('lockout_message', $message);

                throw ValidationException::withMessages([
                    Fortify::username() => $message,
                ]);
            }
        }

        return $next($request);
    }
}
