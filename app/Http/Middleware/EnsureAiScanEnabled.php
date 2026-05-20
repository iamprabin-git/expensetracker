<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAiScanEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isRegularUser() && ! $user->hasAiScanAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'AI Scan is disabled for your account. Contact your administrator.',
                ], 403);
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'AI Scan is disabled for your account. Contact your administrator.');
        }

        return $next($request);
    }
}
