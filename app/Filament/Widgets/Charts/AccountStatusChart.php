<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\InteractsWithAdminAnalytics;
use Filament\Widgets\ChartWidget;

class AccountStatusChart extends ChartWidget
{
    use InteractsWithAdminAnalytics;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Account status mix';

    protected ?string $description = 'Membership and approval distribution';

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $breakdown = $this->analytics()->accountStatusBreakdown();
        $colors = array_slice($this->palette(), 0, count($breakdown['labels']));

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $breakdown['values'],
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $breakdown['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return array_merge($this->baseChartOptions(), [
            'cutout' => '68%',
        ]);
    }
}
