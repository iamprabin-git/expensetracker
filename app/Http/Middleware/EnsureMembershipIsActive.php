<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMembershipIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isRegularUser() && $user->isApproved() && ! $user->hasActiveMembership()) {
            return redirect()->route('account.expired');
        }

        return $next($request);
    }
}
