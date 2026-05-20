<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\Reminder;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;

class AdminPlatformAnalytics
{
    /**
     * @return array<string, int>
     */
    public function userKpis(): array
    {
        $users = User::query()->where('role', UserRole::User);

        $total = (clone $users)->count();
        $approved = (clone $users)->where('is_approved', true)->count();
        $pending = (clone $users)->where('is_approved', false)->count();

        $activeMembership = (clone $users)
            ->where('is_approved', true)
            ->where(function ($query) {
                $query->whereNull('membership_expires_at')
                    ->orWhere('membership_expires_at', '>', now());
            })
            ->count();

        $expiredMembership = (clone $users)
            ->where('is_approved', true)
            ->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '<=', now())
            ->count();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'active_membership' => $activeMembership,
            'expired_membership' => $expiredMembership,
            'google_signups' => (clone $users)->whereNotNull('google_id')->count(),
            'email_signups' => (clone $users)->whereNull('google_id')->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function operationsKpis(): array
    {
        return [
            'pending_reviews' => Review::query()->where('is_approved', false)->count(),
            'unread_messages' => ContactMessage::query()->where('is_read', false)->count(),
            'active_reminders' => Reminder::query()->where('is_active', true)->count(),
            'users_with_records' => User::query()
                ->where('role', UserRole::User)
                ->whereHas('transactions')
                ->count(),
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function registrationsTrend(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();
        $labels = [];
        $values = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = $month->format('M Y');
            $values[] = User::query()
                ->where('role', UserRole::User)
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function accountStatusBreakdown(): array
    {
        $kpis = $this->userKpis();

        return [
            'labels' => ['Active membership', 'Expired membership', 'Pending approval'],
            'values' => [
                $kpis['active_membership'],
                $kpis['expired_membership'],
                $kpis['pending'],
            ],
        ];
    }

    /**
     * Transaction counts only — no amounts (user financial data stays private).
     *
     * @return array{labels: list<string>, values: list<int>}
     */
    public function activityVolumeTrend(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();
        $labels = [];
        $values = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = $month->format('M Y');
            $values[] = Transaction::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function signUpMethodBreakdown(): array
    {
        $kpis = $this->userKpis();

        return [
            'labels' => ['Google', 'Email & password'],
            'values' => [$kpis['google_signups'], $kpis['email_signups']],
        ];
    }

    public function chartPalette(): array
    {
        return ['#4f46e5', '#0ea5e9', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'];
    }
}
