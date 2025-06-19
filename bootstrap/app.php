<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Replace default CSRF middleware with custom one
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        );
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'subscription.limits' => \App\Http\Middleware\CheckSubscriptionLimits::class,
            'subscription.feature' => \App\Http\Middleware\CheckSubscriptionFeature::class,
            'filter.user.data' => \App\Http\Middleware\FilterUserData::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
