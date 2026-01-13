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
            // Auth routes (must be loaded first - contains login, register)
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Admin routes
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Organizer routes
            Route::middleware('web')
                ->group(base_path('routes/organizer.php'));

            // Webhook routes (tanpa CSRF verification)
            Route::middleware('api')
                ->group(base_path('routes/webhook.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'organizer' => \App\Http\Middleware\EnsureUserIsOrganizer::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        // Exclude CSRF verification for webhook routes
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);

        // Throttle limits untuk API routes
        $middleware->throttleApi();

        // Rate limiting groups
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling untuk production
        $exceptions->render(function (\App\Exceptions\InsufficientTicketException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'insufficient_stock',
                    'message' => $e->getMessage(),
                    'ticket_variant_id' => $e->ticketVariantId,
                    'requested' => $e->requested,
                    'available' => $e->available,
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['ticket' => $e->getMessage()])
                ->withInput();
        });

        $exceptions->render(function (\App\Exceptions\PaymentFailedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'payment_failed',
                    'message' => $e->getMessage(),
                    'order_id' => $e->orderId,
                ], 402);
            }

            if ($e->orderId) {
                return redirect()->route('checkout.failed', ['order' => $e->orderId])
                    ->with('error', $e->getMessage());
            }

            return redirect()->back()
                ->withErrors(['payment' => $e->getMessage()]);
        });

        $exceptions->render(function (\App\Exceptions\VoucherInvalidException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'voucher_invalid',
                    'message' => $e->getMessage(),
                    'code' => $e->code,
                    'reason' => $e->reason,
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['voucher' => $e->getMessage()])
                ->withInput();
        });
    })->create();
