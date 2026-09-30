<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Railway terminates HTTPS before forwarding requests to the container.
        if (env('RAILWAY_ENVIRONMENT_ID')) {
            $middleware->trustProxies(at: '*');
        }

        $middleware->alias([
        'role' => EnsureUserHasRole::class,
        'active' => EnsureUserIsActive::class,
        'abilities' => CheckAbilities::class,
        'ability' => CheckForAnyAbility::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    })->create();
