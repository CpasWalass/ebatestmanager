<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockLockedUsers
{
    /**
     * NOTE: Le blocage des comptes verrouillés est géré directement dans
     * FortifyServiceProvider::store() avec le temps restant précis.
     * Ce middleware est conservé pour compatibilité uniquement.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
