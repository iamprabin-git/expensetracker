<?php

namespace App\Console\Commands;

use App\Services\ReminderNotificationService;
use Illuminate\Console\Command;

class SendDueRemindersCommand extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Send email notifications for due financial reminders';

    public function handle(ReminderNotificationService $service): int
    {
        $count = $service->sendDueReminders();

        $this->info("Sent {$count} reminder email(s).");

        return self::SUCCESS;
    }
}
