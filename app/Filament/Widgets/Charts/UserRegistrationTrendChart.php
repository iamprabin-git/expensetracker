<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\InteractsWithAdminAnalytics;
use Filament\Widgets\ChartWidget;

class UserRegistrationTrendChart extends ChartWidget
{
    use InteractsWithAdminAnalytics;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected ?string $heading = 'New user registrations';

    protected ?string $description = 'Monthly sign-ups (last 12 months)';

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $trend = $this->analytics()->registrationsTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Registrations',
                    'data' => $trend['values'],
                    'borderColor' => $this->palette()[0],
                    'backgroundColor' => 'rgba(79, 70, 229, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return array_merge($this->baseChartOptions(), [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.2)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ]);
    }
}
