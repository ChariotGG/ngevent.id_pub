<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // API rate limit (60 requests per minute per IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Checkout rate limit (prevent spam checkout)
        RateLimiter::for('checkout', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()), // 5 per minute
                Limit::perHour(20)->by($request->ip()), // 20 per hour
            ];
        });

        // Login rate limit (prevent brute force)
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

        // Email verification rate limit
        RateLimiter::for('verification', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
        });
    }
}
