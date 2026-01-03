<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class XenditWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $callbackToken = $request->header('x-callback-token');
        $expectedToken = config('xendit.webhook_token');

        if (!$callbackToken || $callbackToken !== $expectedToken) {
            Log::channel('webhook')->warning('Invalid Xendit webhook signature', [
                'ip' => $request->ip(),
                'token_provided' => !empty($callbackToken),
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
