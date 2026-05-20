<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class UserPasswordResetNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' password')
            ->greeting('Hello!')
            ->line('You requested a password reset for your '.config('app.name').' account.')
            ->action('Reset password', $url)
            ->line("This link expires in {$expire} minutes.")
            ->line('If you did not request a reset, you can ignore this email.');
    }
}
