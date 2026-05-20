<?php

namespace App\Filament\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function request(): void
    {
        $email = $this->form->getState()['email'] ?? null;

        $isAdmin = $email && User::query()
            ->where('email', $email)
            ->where('role', UserRole::Admin)
            ->exists();

        if (! $isAdmin) {
            Notification::make()
                ->title(__('passwords.sent'))
                ->body(__('filament-panels::auth/pages/password-reset/request-password-reset.notifications.sent.body'))
                ->success()
                ->send();

            $this->form->fill();

            return;
        }

        parent::request();
    }
}
