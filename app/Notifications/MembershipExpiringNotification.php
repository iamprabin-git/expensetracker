<?php

namespace App\Notifications;

use App\Notifications\Concerns\FormatsDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class MembershipExpiringNotification extends Notification
{
    use FormatsDatabaseNotification, Queueable;

    public function __construct(
        public Carbon $expiresAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->databasePayload(
            category: 'membership',
            title: 'Membership expiring soon',
            message: 'Your membership expires on '.$this->expiresAt->format('M d, Y').'. Contact support to renew.',
            actionUrl: route('account.expired'),
            actionLabel: 'Learn more',
            extra: [
                'expires_at' => $this->expiresAt->toIso8601String(),
            ],
        );
    }
}
