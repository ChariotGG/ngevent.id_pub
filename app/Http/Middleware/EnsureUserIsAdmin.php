<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Check email verification
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Verifikasi email Anda terlebih dahulu');
        }

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}
