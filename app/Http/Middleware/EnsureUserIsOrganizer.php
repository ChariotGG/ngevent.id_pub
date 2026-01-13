<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = auth()->user();

        // Check email verification
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Verifikasi email Anda terlebih dahulu');
        }

        // Admin can access organizer panel
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user is organizer
        if (!$user->isOrganizer()) {
            abort(403, 'Anda tidak memiliki akses sebagai organizer. Silakan daftar sebagai organizer terlebih dahulu.');
        }

        // Check if organizer profile exists
        if (!$user->organizer) {
            abort(403, 'Profil organizer Anda belum lengkap. Silakan hubungi admin.');
        }

        return $next($request);
    }
}
