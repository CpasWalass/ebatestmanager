<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenancy for Laravel Configuration
    |--------------------------------------------------------------------------
    |
    | This package provides multi-tenancy for Laravel applications.
    |
    */

    'tenant_model' => \App\Models\Tenant::class,

    /*
    |--------------------------------------------------------------------------
    | Identification Strategy
    |--------------------------------------------------------------------------
    |
    | The strategy used to identify the current tenant.
    | Supported: 'domain', 'path', 'header', 'user'
    |
    */
    'identification_strategy' => env('TENANCY_IDENTIFICATION_STRATEGY', 'domain'),

    /*
    |--------------------------------------------------------------------------
    | Domain Configuration
    |--------------------------------------------------------------------------
    |
    | If using domain-based identification, configure it here.
    |
    */
    'domain' => [
        'central_domain' => env('APP_URL', 'http://localhost:8000'),
        'suffix' => env('TENANCY_DOMAIN_SUFFIX', '.localhost:8000'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Isolation
    |--------------------------------------------------------------------------
    |
    | Configure how data is isolated per tenant.
    | Supported: 'database', 'schema', 'shared' (with scoped queries)
    |
    */
    'isolation' => [
        'type' => env('TENANCY_ISOLATION_TYPE', 'shared'),
        'use_separate_database' => env('TENANCY_SEPARATE_DB', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenancy Middleware
    |--------------------------------------------------------------------------
    |
    | Configure the middleware that identifies and initializes the tenant.
    |
    */
    'middleware' => [
        'identify_tenant' => \App\Http\Middleware\IdentifyTenant::class,
        'eager_load_tenant' => \App\Http\Middleware\EagerLoadTenant::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache tenant data to improve performance.
    |
    */
    'cache' => [
        'enabled' => true,
        'store' => 'redis',
        'ttl' => 3600, // 1 hour
    ],
];
