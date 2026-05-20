<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminPrivacyBannerWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-privacy-banner';
}
