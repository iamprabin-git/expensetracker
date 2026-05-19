<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\MembershipExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyExpiringMembershipsCommand extends Command
{
    protected $signature = 'memberships:notify-expiring {--days=7 : Days before expiry to notify}';

    protected $description = 'Notify users whose membership expires within the given days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $windowStart = now();
        $windowEnd = now()->addDays($days);

        $users = User::query()
            ->where('role', UserRole::User)
            ->where('is_approved', true)
            ->whereNotNull('membership_expires_at')
            ->whereBetween('membership_expires_at', [$windowStart, $windowEnd])
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $alreadyNotified = $user->notifications()
                ->where('type', MembershipExpiringNotification::class)
                ->where('created_at', '>=', now()->subDays($days))
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            $user->notify(new MembershipExpiringNotification(Carbon::parse($user->membership_expires_at)));
            $sent++;
        }

        $this->info("Sent {$sent} membership expiry notification(s).");

        return self::SUCCESS;
    }
}
