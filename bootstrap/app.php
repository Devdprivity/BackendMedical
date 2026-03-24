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
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware
        $middleware->web(append: [
            \App\Http\Middleware\CheckOnboarding::class,
        ]);

        // Add cookie decryption + session to API middleware so auth:web guard can read sessions
        $middleware->api(prepend: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
        ]);
        $middleware->api(append: [
            \Illuminate\Session\Middleware\StartSession::class,
        ]);
        
        // Alias middleware
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'check.subscription.feature' => \App\Http\Middleware\CheckSubscriptionFeature::class,
            'check.subscription.limits' => \App\Http\Middleware\CheckSubscriptionLimits::class,
            'filter.user.data' => \App\Http\Middleware\FilterUserData::class,
            'onboarding' => \App\Http\Middleware\CheckOnboarding::class,
            'check.payment.setup' => \App\Http\Middleware\CheckPaymentSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
