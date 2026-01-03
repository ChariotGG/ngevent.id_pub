<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isOrganizer() && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses sebagai organizer');
        }

        if (!auth()->user()->organizer) {
            abort(403, 'Anda belum terdaftar sebagai organizer');
        }

        return $next($request);
    }
}
