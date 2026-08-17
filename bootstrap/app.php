<?php

use App\Http\Middleware\CheckIsActive;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\RedirectByRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // IdentifyTenant / EagerLoadTenant / BlockLockedUsers retirés :
        // tenancy simplifiée (un seul tenant, plus de résolution dynamique),
        // et le verrouillage de compte est désormais géré dans
        // FortifyServiceProvider::authenticateUsing() (voir README_REFONTE.md).
        $middleware->web(append: [
            RedirectByRole::class,
            CheckIsActive::class,
            ForcePasswordChange::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
