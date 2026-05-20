<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Filament\Resources\Users\UserResource;
use App\Services\AdminPlatformAnalytics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformKpiOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Platform KPIs';

    protected ?string $description = 'Operational metrics — no user financial amounts';

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $analytics = app(AdminPlatformAnalytics::class);
        $users = $analytics->userKpis();
        $ops = $analytics->operationsKpis();

        return [
            Stat::make('Total users', (string) $users['total'])
                ->description("{$users['approved']} approved · {$users['pending']} pending")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(UserResource::getUrl('index')),
            Stat::make('Active memberships', (string) $users['active_membership'])
                ->description("{$users['expired_membership']} expired")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Pending approvals', (string) $users['pending'])
                ->description('Awaiting admin action')
                ->descriptionIcon('heroicon-m-clock')
                ->color($users['pending'] > 0 ? 'warning' : 'gray')
                ->url(UserResource::getUrl('index')),
            Stat::make('Unread messages', (string) $ops['unread_messages'])
                ->description("{$ops['pending_reviews']} reviews pending")
                ->descriptionIcon('heroicon-m-envelope')
                ->color($ops['unread_messages'] > 0 ? 'danger' : 'gray')
                ->url(ContactMessageResource::getUrl('index')),
            Stat::make('Active reminders', (string) $ops['active_reminders'])
                ->description('Scheduled user reminders')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('info'),
            Stat::make('Users logging data', (string) $ops['users_with_records'])
                ->description('Have at least one record (count only)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
            Stat::make('Google sign-ups', (string) $users['google_signups'])
                ->description("{$users['email_signups']} email registrations")
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
            Stat::make('Reviews queue', (string) $ops['pending_reviews'])
                ->description('Pending moderation')
                ->descriptionIcon('heroicon-m-star')
                ->color($ops['pending_reviews'] > 0 ? 'warning' : 'gray')
                ->url(ReviewResource::getUrl('index')),
        ];
    }
}
