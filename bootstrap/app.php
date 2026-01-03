<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin routes
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Organizer routes
            Route::middleware('web')
                ->group(base_path('routes/organizer.php'));

            // Auth routes
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Webhook routes (tanpa CSRF)
            Route::middleware('web')
                ->group(base_path('routes/webhook.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'organizer' => \App\Http\Middleware\EnsureUserIsOrganizer::class,
            'xendit.webhook' => \App\Http\Middleware\XenditWebhookSignature::class,
            'throttle.checkout' => \App\Http\Middleware\ThrottleCheckout::class,
        ]);

        // Disable CSRF for webhook routes
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
