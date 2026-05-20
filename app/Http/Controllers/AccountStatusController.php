<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountStatusController extends Controller
{
    public function pending(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isApproved()) {
            return redirect()->route('dashboard');
        }

        return view('account.pending');
    }

    public function expired(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasActiveMembership()) {
            return redirect()->route('dashboard');
        }

        return view('account.expired', [
            'expiresAt' => $user?->membership_expires_at,
        ]);
    }
}
