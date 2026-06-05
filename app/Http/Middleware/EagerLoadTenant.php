<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EagerLoadTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Share tenant with views if initialized
            if (tenancy()->tenant) {
                $tenant = tenant();
                view()->share('currentTenant', $tenant);
                $request->attributes->set('tenant', $tenant);
            }
        } catch (\Exception $e) {
            // Silently fail if tenant can't be loaded (e.g., during auth routes)
        }

        return $next($request);
    }
}
