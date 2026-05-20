<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\InteractsWithAdminAnalytics;
use Filament\Widgets\ChartWidget;

class PlatformActivityChart extends ChartWidget
{
    use InteractsWithAdminAnalytics;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Platform activity volume';

    protected ?string $description = 'Number of records logged per month — amounts are not shown';

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $trend = $this->analytics()->activityVolumeTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Records logged',
                    'data' => $trend['values'],
                    'backgroundColor' => 'rgba(14, 165, 233, 0.75)',
                    'borderColor' => '#0ea5e9',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
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
                    'title' => [
                        'display' => true,
                        'text' => 'Record count',
                    ],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.2)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ]);
    }
}
