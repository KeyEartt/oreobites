<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude PayMongo webhook from CSRF
        $middleware->validateCsrfTokens(except: [
            'api/webhook',
        ]);

        // Register custom middleware aliases
        $middleware->alias([
    'require.staff' => \App\Http\Middleware\RequireStaff::class,
    'require.admin' => \App\Http\Middleware\RequireAdmin::class,
    'require.customer' => \App\Http\Middleware\RequireCustomer::class, // 🆕
]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();