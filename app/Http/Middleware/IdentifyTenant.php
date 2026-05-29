<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use App\Models\Tenant;
use App\Models\User;

class IdentifyTenant
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
        // Attempt 1: Identify tenant by authenticated user
        if ($user = auth()->user()) {
            $tenantId = $this->getTenantIdForUser($user);
            if ($tenantId) {
                $this->initializeTenant($tenantId);
                return $next($request);
            }
        }

        // Attempt 2: Identify tenant by domain/subdomain
        $tenantId = $this->getTenantIdByDomain($request->host());
        if ($tenantId) {
            $this->initializeTenant($tenantId);
            return $next($request);
        }

        // Attempt 3: Identify tenant from request header
        if ($tenantId = $request->header('X-Tenant-ID')) {
            $this->initializeTenant($tenantId);
            return $next($request);
        }

        // No tenant identified - allow for central app routes
        return $next($request);
    }

    /**
     * Initialize the tenant context
     */
    protected function initializeTenant($tenantId)
    {
        if ($tenant = Tenant::find($tenantId)) {
            tenancy()->initialize($tenant);
        }
    }

    /**
     * Get tenant ID from authenticated user
     */
    protected function getTenantIdForUser(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        // User belongs to a tenant through their Client relationship
        // Assuming User has a client_id or similar
        // For now, we'll check if they have a tenant_id attribute
        if ($user->hasAttribute('tenant_id')) {
            return $user->tenant_id;
        }

        // Alternative: If user is associated via a Client
        if ($user->hasAttribute('client_id') && $user->client_id) {
            $client = \App\Models\Client::find($user->client_id);
            return $client?->tenant_id;
        }

        return null;
    }

    /**
     * Get tenant ID by domain/subdomain
     */
    protected function getTenantIdByDomain($host): ?string
    {
        // Remove port from host if present
        $host = explode(':', $host)[0];

        // Check for subdomain pattern: subdomain.domain.tld
        $parts = explode('.', $host);

        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            // Exclude common subdomains (www, mail, etc.)
            if (!in_array($subdomain, ['www', 'mail', 'admin', 'localhost'])) {
                $tenant = Tenant::where('id', $subdomain)->first();
                if ($tenant) {
                    return $tenant->id;
                }
            }
        }

        return null;
    }
}
