<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleCheckout
{
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'checkout:' . ($request->user()?->id ?? $request->ip());

        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.',
                ], 429);
            }

            return back()->withErrors([
                'checkout' => 'Terlalu banyak percobaan checkout. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ]);
        }

        $this->limiter->hit($key, 60); // 5 attempts per minute

        return $next($request);
    }
}
