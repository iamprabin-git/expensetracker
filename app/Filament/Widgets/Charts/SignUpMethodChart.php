<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\InteractsWithAdminAnalytics;
use Filament\Widgets\ChartWidget;

class SignUpMethodChart extends ChartWidget
{
    use InteractsWithAdminAnalytics;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 5;

    protected ?string $heading = 'Sign-up channels';

    protected ?string $description = 'How users registered on the platform';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $breakdown = $this->analytics()->signUpMethodBreakdown();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $breakdown['values'],
                    'backgroundColor' => [$this->palette()[0], $this->palette()[2]],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $breakdown['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return $this->baseChartOptions();
    }
}
