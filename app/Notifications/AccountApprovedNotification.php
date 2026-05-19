<?php

namespace App\Notifications;

use App\Notifications\Concerns\FormatsDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification
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
            title: 'Account approved',
            message: 'Your account has been approved. You can now access the full dashboard.',
            actionUrl: route('dashboard'),
            actionLabel: 'Go to dashboard',
        );
    }
}
