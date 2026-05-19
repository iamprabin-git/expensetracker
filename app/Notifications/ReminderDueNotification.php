<?php

namespace App\Notifications;

use App\Models\Reminder;
use App\Notifications\Concerns\FormatsDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReminderDueNotification extends Notification
{
    use FormatsDatabaseNotification, Queueable;

    public function __construct(
        public Reminder $reminder,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $amount = $this->reminder->formattedAmount($notifiable instanceof \App\Models\User ? $notifiable : null);
        $message = $this->reminder->title;

        if ($this->reminder->payee_name) {
            $message .= ' — '.$this->reminder->payee_name;
        }

        if ($amount) {
            $message .= ' ('.$amount.')';
        }

        return $this->databasePayload(
            category: 'reminder',
            title: 'Reminder due',
            message: $message,
            actionUrl: route('reminders.index'),
            actionLabel: 'View reminders',
            extra: [
                'reminder_id' => $this->reminder->id,
            ],
        );
    }
}
