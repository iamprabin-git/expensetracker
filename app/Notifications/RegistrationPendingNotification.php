<?php

namespace App\Notifications;

use App\Notifications\Concerns\FormatsDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationPendingNotification extends Notification
{
    use FormatsDatabaseNotification, Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->databasePayload(
            category: 'account',
            title: 'Registration received',
            message: 'Your account is pending administrator approval. We emailed your login details.',
            actionUrl: route('account.pending'),
            actionLabel: 'View status',
        );
    }
}
