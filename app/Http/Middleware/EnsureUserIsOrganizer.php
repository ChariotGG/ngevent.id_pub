<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, [UserRole::ORGANIZER, UserRole::ADMIN])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            abort(403, 'Akses tidak diizinkan');
        }

        // Ensure organizer profile exists
        if ($user->role === UserRole::ORGANIZER && !$user->organizer) {
            return redirect()->route('organizer.profile.create')
                ->with('warning', 'Silakan lengkapi profil organizer Anda terlebih dahulu');
        }

        return $next($request);
    }
}
