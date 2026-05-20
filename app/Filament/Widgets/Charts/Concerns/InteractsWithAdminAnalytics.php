<?php

namespace App\Filament\Widgets\Charts\Concerns;

use App\Services\AdminPlatformAnalytics;

trait InteractsWithAdminAnalytics
{
    protected function analytics(): AdminPlatformAnalytics
    {
        return app(AdminPlatformAnalytics::class);
    }

    protected function palette(): array
    {
        return $this->analytics()->chartPalette();
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseChartOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
}
