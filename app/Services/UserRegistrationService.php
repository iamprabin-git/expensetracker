<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Mail\NewUserPendingAdminMail;
use App\Mail\NewUserWelcomeCredentialsMail;
use App\Models\User;
use App\Notifications\RegistrationPendingNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserRegistrationService
{
    public function register(
        string $name,
        string $email,
        string $plainPassword,
        ?string $googleId = null,
        ?string $googleToken = null,
        bool $emailVerified = false,
    ): User {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'role' => UserRole::User,
            'is_approved' => false,
            'google_id' => $googleId,
            'google_token' => $googleToken,
            'email_verified_at' => $emailVerified ? now() : null,
        ]);

        event(new Registered($user));

        $this->sendRegistrationEmails($user, $plainPassword);

        return $user;
    }

    public function sendRegistrationEmails(User $user, string $plainPassword): void
    {
        Mail::to($this->adminNotificationEmail())->send(new NewUserPendingAdminMail($user));

        Mail::to($user->email)->send(new NewUserWelcomeCredentialsMail($user, $plainPassword));

        $user->notify(new RegistrationPendingNotification);
    }

    public function adminNotificationEmail(): string
    {
        $configured = config('mail.admin_notification_email');

        if (filled($configured)) {
            return $configured;
        }

        $adminEmail = User::query()
            ->where('role', UserRole::Admin)
            ->value('email');

        return $adminEmail ?? config('mail.from.address');
    }
}
