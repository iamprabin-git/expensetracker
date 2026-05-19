<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(UserRegistrationService $registration): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google sign-in was cancelled or failed. Please try again or use your email and password.']);
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            Auth::login($user, remember: true);

            return $this->redirectAuthenticated($user);
        }

        $plainPassword = Str::password(12);

        $user = $registration->register(
            name: $googleUser->getName() ?? Str::before($googleUser->getEmail(), '@'),
            email: $googleUser->getEmail(),
            plainPassword: $plainPassword,
            googleId: $googleUser->getId(),
            googleToken: $googleUser->token,
            emailVerified: true,
        );

        Auth::login($user, remember: true);

        return redirect()->route('account.pending');
    }

    protected function redirectAuthenticated(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->intended('/admin');
        }

        if (! $user->isApproved()) {
            return redirect()->route('account.pending');
        }

        if (! $user->hasActiveMembership()) {
            return redirect()->route('account.expired');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
