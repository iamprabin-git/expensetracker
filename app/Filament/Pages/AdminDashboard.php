<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminPrivacyBannerWidget;
use App\Filament\Widgets\Charts\AccountStatusChart;
use App\Filament\Widgets\Charts\PlatformActivityChart;
use App\Filament\Widgets\Charts\SignUpMethodChart;
use App\Filament\Widgets\Charts\UserRegistrationTrendChart;
use App\Filament\Widgets\PlatformKpiOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\WidgetConfiguration;

class AdminDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Platform intelligence';

    protected static ?int $navigationSort = -2;

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AdminPrivacyBannerWidget::class,
            PlatformKpiOverview::class,
            UserRegistrationTrendChart::class,
            AccountStatusChart::class,
            PlatformActivityChart::class,
            SignUpMethodChart::class,
        ];
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }
}
