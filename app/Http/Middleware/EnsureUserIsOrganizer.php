<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin juga bisa akses organizer dashboard
        if (!$user->isOrganizer() && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses sebagai organizer');
        }

        // Cek apakah user punya data organizer
        if (!$user->organizer && !$user->isAdmin()) {
            abort(403, 'Anda belum terdaftar sebagai organizer. Silakan daftar sebagai organizer terlebih dahulu.');
        }

        return $next($request);
    }
}
