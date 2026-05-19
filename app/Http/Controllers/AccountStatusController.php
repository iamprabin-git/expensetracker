<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountStatusController extends Controller
{
    public function pending(Request $request): View
    {
        if ($request->user()?->isApproved()) {
            return redirect()->route('dashboard');
        }

        return view('account.pending');
    }

    public function expired(Request $request): View
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
