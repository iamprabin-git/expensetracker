<?php

namespace App\Services;

use App\Mail\ReminderDueMail;
use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderNotificationService
{
    public function sendDueReminders(): int
    {
        $sent = 0;

        Reminder::query()
            ->dueNow()
            ->with('user')
            ->orderBy('id')
            ->chunkById(50, function ($reminders) use (&$sent) {
                foreach ($reminders as $reminder) {
                    if ($this->processDueReminder($reminder)) {
                        $sent++;
                    }
                }
            });

        return $sent;
    }

    public function processDueReminder(Reminder $reminder): bool
    {
        if (! $reminder->user) {
            return false;
        }

        try {
            $processed = false;

            DB::transaction(function () use ($reminder, &$processed) {
                $locked = Reminder::query()
                    ->whereKey($reminder->id)
                    ->lockForUpdate()
                    ->first();

                if (! $locked || ! $locked->is_active || $locked->next_remind_at->isFuture()) {
                    return;
                }

                if ($locked->notify_email) {
                    Mail::to($locked->user->email)->send(new ReminderDueMail($locked));
                }

                $locked->user->notify(new ReminderDueNotification($locked));
                $locked->advanceAfterNotification();

                $processed = true;
            });

            return $processed;
        } catch (\Throwable $exception) {
            Log::error('Failed to process due reminder', [
                'reminder_id' => $reminder->id,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
